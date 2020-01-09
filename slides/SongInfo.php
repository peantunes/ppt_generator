<?php

use PhpOffice\PhpPresentation\DocumentLayout;

class SongInfo {

    var $title = "My little song";
    var $content = "";
    var $authorName = "Pedro";

    public function giveMeTheBlocks() {
        $rows = explode(PHP_EOL, $this->content);
        $finalContent = array();
        $previousRow = "";
        for ($i = 0; $i<count($rows); $i++) {
            $row = trim($rows[$i]);
            //verify if row is a chord pattern
            $all_matchs = preg_match_all('/(\\(*[CDEFGAB](?:b|bb)*(?:#|##|sus|maj|min|aug|m|M|°|\\+|[0-9])*[\\(]?[\\d\\/]*[\\)]?(?:[CDEFGAB](?:b|bb)*(?:#|##|sus|maj|min|aug|m|M|°|\\+|[0-9])*[\\d\\/]*[\\)]?)*\\)*)(?=[\\s|$])(?! [a-z])/', $row);
            $length = strlen($row);
            if ($all_matchs == false &&  ($length > 2 || $length == 0)) {
                if ($row == "") {
                    if ($previousRow != ""){
                        array_push($finalContent, $previousRow);
                    }
                    $previousRow = "";
                }else{
                    if ($previousRow != ""){
                        $previousRow .= PHP_EOL;
                    }
                    $previousRow .= $this->cleanRow($row);
                }
            }
        }
        if ($previousRow != "") {
            array_push($finalContent, $previousRow);
        }
        return $finalContent;
    }

    public function cleanRow($text) {
        $cleanText = preg_replace('/([\t.]*)/','',$text,-1,$count);
        // echo $cleanText.PHP_EOL;
        
        $words = explode(' ', $cleanText);
        $newRow = array();
        for ($i = 0; $i< count($words) ; $i++) {
            $word = $words[$i];
            if (strlen($word) > 0) {
                // echo $word.PHP_EOL;
                array_push($newRow, $word);
            }
        }
        $result = implode(' ', $newRow);
        // echo $result.PHP_EOL;
        
        return $result;
    }
}