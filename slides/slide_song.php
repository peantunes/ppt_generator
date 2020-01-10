<?php

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\DocumentLayout;

require_once 'SongInfo.php';

class SlideGenerator {

    const SLIDE_LIMIT_CHARS = 36;
    const SLIDE_LIMIT_ROWS = 8;
    const SLIDE_LAYOUT = DocumentLayout::LAYOUT_SCREEN_16X10;

    var $backgroundImageURL;
    var $decoration;
    var $presentation;

    public function firstSlide($title, $image) {

        $oSlide = $this->presentation->getActiveSlide();
        $shapeImage = $oSlide->createDrawingShape();
        $shapeImage->setName('backgroundImage')
                ->setDescription('Description of the drawing')
                ->setPath($image)
                ->setHeight(640) //680
                ->setWidth(1000) //960
                ->setOffsetX(-20)
                ->setOffsetY(-20);


        $shape = $oSlide->createRichTextShape();
        $shape->setHeight(680) //680
            // ->setWidth(960) //960
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER )
                                                ->setVertical( Alignment::VERTICAL_CENTER);
        $textRun = $shape->createTextRun($title);
        $textRun->getFont()->setBold(true)
                            ->setSize(60)
                            ->setColor( new Color(Color::COLOR_WHITE) );

        $shape->getShadow()->setVisible(true)
                            ->setDirection(45)
                            ->setDistance(10);
        return $oSlide;
    }

    var $clean_array = array(
        '/[áàâãªä]/u'   =>   'a',
        '/[íìîï]/u'     =>   'i',
        '/[éèêë]/u'     =>   'e',
        '/[óòôõºö]/u'   =>   'o',
        '/[úùûü]/u'     =>   'u',
        '/ç/'           =>   'c',
        '/ñ/'           =>   'n',
        '/ /'           =>   '_'
    );
    public function genericDivision($title) {

        $oSlide = $this->presentation->createSlide();
        $shapeImage = $oSlide->createDrawingShape();
        $image = preg_replace(array_keys($this->clean_array), array_values($this->clean_array), strtolower($title));
        $shapeImage->setName('backgroundImage')
                ->setDescription('Description of the drawing')
                ->setPath("./resources/$image.jpg")
                ->setHeight(680) //680
                // ->setWidth(960) //960
                ->setOffsetX(0)
                ->setOffsetY(0);


        $shape = $oSlide->createRichTextShape();
        $shape->setHeight(400) //680
            ->setWidth(800) //960
            ->setOffsetX(130)
            ->setOffsetY(40);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT )
                                                ->setVertical( Alignment::VERTICAL_TOP);
        $textRun = $shape->createTextRun($title);
        $textRun->getFont()->setBold(true)
                            ->setSize(60)
                            ->setColor( new Color(Color::COLOR_YELLOW) );

        $shape->getShadow()->setVisible(true)
                            ->setDirection(45)
                            ->setDistance(10);
        return $oSlide;
    }

    public function genericSongSlide($text) {
        $slides = $this->organiseTextForSlides($text);
        $slideList = array();
        for($i = 0; $i < count($slides); $i++) {
            $slideText = $slides[$i];
            // echo $slideText."\n\n";
            array_push($slideList, $this->createSlide($slideText));
        }
        return $slideList;
    }
    private function createSlide($text) {
        $oSlide = $this->presentation->createSlide();
        
        $topImage = $oSlide->createDrawingShape();
        $topImage->setName('top_song_image')
                // ->setDescription('Description of the drawing')
                ->setPath('./resources/song_image/top.jpg')
                ->setWidth(141) //960
                // ->setHeight(94) //680
                ->setOffsetX(0)
                ->setOffsetY(0);


        $bottomImage = $oSlide->createDrawingShape();
        $bottomImage->setName('bottom_song_image')
                // ->setDescription('Description of the drawing')
                ->setPath('./resources/song_image/bottom.jpg')
                ->setWidth(141) //960
                ->setOffsetX(790)
                ->setOffsetY(510);

        $shape = $oSlide->createRichTextShape();
        $shape->setHeight(580) //600
            ->setWidth(940) //960
            ->setOffsetX(10)
            ->setOffsetY(10);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER )
                                                ->setVertical( Alignment::VERTICAL_CENTER);

        $textRun = $shape->createTextRun(str_replace(PHP_EOL,"\n", $text));
        $textRun->getFont()->setBold(true)
                            ->setSize(40)
                            ->setColor( new Color(Color::COLOR_BLACK) );
        return $oSlide;
    }

    private function masterSlideSong() {
        $oMaster = $this->presentation->createMasterSlide();
    }

    /**
     * This function returns an array with the 
     * number of slides and it's text
     */
    public function organiseTextForSlides($text) {
        $rows = explode(PHP_EOL, $text);
        $result = "";
        for ($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];
            $result .= $this->validateColumns($row);
        }
        return $this->limitRows($result);
    }
    
    /**
     * Verify the number of rows is valid
     * if not split in more items
     */
    private function limitRows($text) {
        if ($text == "") {
            return array();
        }
        $rows = explode(PHP_EOL,$text);
        if (count($rows) > SlideGenerator::SLIDE_LIMIT_ROWS) {
            $slide1 = implode(PHP_EOL, array_slice($rows, 0, SlideGenerator::SLIDE_LIMIT_ROWS - 1));
            return array_merge(array($slide1),$this->limitRows(implode(PHP_EOL, array_slice($rows, SlideGenerator::SLIDE_LIMIT_ROWS))));
        }
        return array($text);
    }
    private function validateColumns($text) {
        if ($text == "") {
            return "";
        }
        $size = strlen($text);
        if ($size > SlideGenerator::SLIDE_LIMIT_CHARS) {
            $position = strpos($text, " ", SlideGenerator::SLIDE_LIMIT_CHARS/2);
            $row1 = substr($text, 0, $position);
            $row2 = substr($text, $position + 1);
            return $row1.PHP_EOL.$this->validateColumns(trim($row2));
        }
        return $text.PHP_EOL;
    }
}

// $songInfo = new SongInfo();
// $slideGenerator = new SlideGenerator();
// $blocks = $songInfo->giveMeTheBlocks();
// for ($i = 0; $i < count($blocks) ; $i++) {
//     $block = $blocks[$i];

//     $content = $slideGenerator->organiseTextForSlides($block);
//     for($j = 0; $j < count($content); $j++){
//         $slide = str_replace(PHP_EOL, "<br />", $content[$j]);
//         echo "<div class=\"slide\">$slide</div>";
//     }
// }
    /*
        
        
        $shape = $currentSlide->createDrawingShape();

        $shape->setName('PHPPresentation logo')
                ->setDescription('PHPPresentation logo')
                ->setPath('imagem-landing-advento-2.png')
                ->setHeight(36)
                ->setOffsetX(10)
                ->setOffsetY(10);
        $shape->getShadow()->setVisible(true)
                            ->setDirection(45)
                            ->setDistance(10);

        // Create a shape (text)
        $shape = $currentSlide->createRichTextShape()
                            ->setHeight(300)
                            ->setWidth(600)
                            ->setOffsetX(170)
                            ->setOffsetY(180);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        $textRun = $shape->createTextRun('Thank you for using PHPPresentation!');
        $textRun->getFont()->setBold(true)
                            ->setSize(60)
                            ->setColor( new Color( 'FFE06B20' ) );
    }
}
?>
*/