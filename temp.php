<?php 
require_once 'libs/PhpPresentation/Autoloader.php';
\PhpOffice\PhpPresentation\Autoloader::register();

require_once 'libs/Common/Autoloader.php';
\PhpOffice\Common\Autoloader::register();


use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\DocumentLayout;

require_once 'slides/slide_song.php';
require_once 'slides/SongInfo.php';

$objPHPPowerPoint = new PhpPresentation();
$layout = new DocumentLayout();
$layout->setDocumentLayout(SlideGenerator::SLIDE_LAYOUT);
$objPHPPowerPoint->setLayout($layout);

// Create slide
// $currentSlide = $objPHPPowerPoint->getActiveSlide();

$slideGenerator = new SlideGenerator();
$slideGenerator->presentation = $objPHPPowerPoint;

$url = $_REQUEST["url"];

$jsonContent = file_get_contents($url);
$jsonContent = mb_convert_encoding($jsonContent, "UTF-8");
$json_a = json_decode($jsonContent, true);
$songs = $json_a['musicas'];
$labelDivision = 'momento';
$date = "";
if(strpos($url, 'sundaymass')) {
    $date = date('d/m/Y',strtotime($json_a['dtcatalogo']["date"]));
    $labelDivision = 'categoria';
}else{

    $date = date('d/m/Y', $json_a['dtmissa']);
}
$slideGenerator->firstSlide($json_a["nome"].PHP_EOL.$date);

for ($m = 0; $m < count($songs); $m++) {
    $song = $songs[$m];
    $songInfo = new SongInfo();
    $songInfo->content = $song['cifra'];
    
    $slideGenerator->genericDivision($song[$labelDivision]);

    $blocks = $songInfo->giveMeTheBlocks();
    for ($i = 0; $i < count($blocks) ; $i++) {
        $block = $blocks[$i];

        $slideGenerator->genericSongSlide($block);
    }
}

$oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
$oWriterPPTX->save(__DIR__ . "/generated/sample.pptx");
// $oWriterODP = IOFactory::createWriter($objPHPPowerPoint, 'ODPresentation');
// $oWriterODP->save(__DIR__ . "/generated/sample.odp");

// header('location: http://appmissa.reidasnacoes.com/slide/generated/sample.pptx');

?>