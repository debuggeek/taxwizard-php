<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/24/16
 * Time: 1:41 PM
 */

include_once "defines.php";

class HTMLTable
{

    private $tableType;

    private $table;

    function toHTML(){
        $resultHTML = $this->generateHeader();
        $resultHTML = $resultHTML . $this->generateHtmlTitle();
        $resultHTML = $resultHTML . $this->table;
        $resultHTML = $resultHTML . $this->generateFooter();
        return $resultHTML;
    }

    public function parseJson($jsonData){
        $this->table = $this->generateTable($jsonData);
    }

    private function generateTable($jsonData){
        $json = json_decode($jsonData, true);
        $table = '<table>';
        $table = $table . $this->generateTableHeader($json['compCount']);
        $jsonRows = $json['rows'];
        foreach($jsonRows as $row){
            $table = $table . $this->generateRow($row);
        }
        $table = $table .'</table>';
        return $table;
    }

    private function generateTableHeader($compCount, $start=null, $end=null){
        $start = $start === null ? 1 : $start;
        $end = $end === null ? $compCount : $end;
        $header = '<tr><th></th><th class=\'colhead\'> Subject <div id=\'subject\'/></th>'; // cell 0,0, empty

        for($i = $start; $i <= $end; $i++){
            $header = $header . '<th class=\'colhead\'> Comp #' . $i . '</th>';
        }
        $header= $header . '</tr>';
        return $header;
    }

    private function generateRow($jsonRowData){
        $result = '<tr>';
        $rowClass = null;
        foreach ($jsonRowData as $key => $value){
            if($key == 'description'){
                if($value != null) {
                    $rowClass = str_replace(' ','',$value);
                    $result = $result . '<th>' . $value . '</th>';
                } else {
                    $result = $result . '<td class=\'desc\'></td>';
                }
            } else if (is_array($value)){
                //We have a multi value cell
                $cell = '<td>';
                if(array_key_exists('value', $value)) {
                   $cell = $cell . '<div class=\'value\'>' . $value['value'] . '</div>';
                }
                if(array_key_exists('subvalue',$value)){
                    $cell = $cell . '<div class=\'subvalue\'>' . $value['subvalue'] . '</div>';
                }
                if(array_key_exists('delta',$value)){
                    if ($key != 'col1') {
                        $cell = $cell . '<div class=\'delta\'>' . $value['delta'] . '</div>';
                    }
                }
                $cell = $cell . '</td>';
                $result = $result . $cell;
            } else {
                //Just a value
                if($value != null){
                    if ($this->isTableElementGlobal($rowClass) && $key != 'col1') {
                        $result = $result . '<td>/td>';
                    } else {
                        $result = $result . '<td class=\'' . $rowClass . '\'>' . $value . '</td>';
                    }
                } else {
                    $result = $result . '<td class=\'filler\'><td/>';
                }
            }
        }
        $result = $result . '</tr>';
        return $result;
    }

    private function isTableElementGlobal($key){
        global $MEANVAL, $MEANVALSQFT, $MEDIANVAL, $MEDIANVALSQFT;
        switch($key){
            case str_replace(' ','',$MEANVAL[0]):
            case str_replace(' ','',$MEANVALSQFT[0]):
            case str_replace(' ','',$MEDIANVAL[0]):
            case str_replace(' ','',$MEDIANVALSQFT[0]):
                return true;
            default:
                return false;
        }
    }

    private function generateHtmlTitle(){
        return '<h2>Comp '. $this->tableType .'Grid - Five Stone - ' . date('l jS \of F Y h:i:s A') . '</h2>';
    }
    
    private function generateHeader(){
        return '<html><head><link rel="stylesheet" type="text/css" href="default.css"/></head><body>';
    }

    private function generateFooter(){
        return '</body></html>';
    }
}