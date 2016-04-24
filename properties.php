<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 6/9/14
 * Time: 6:12 PM
 */

session_start();
include 'library/FullTable.php';
include 'library/presentation.php';

$debug = false;

$compInfo = array();

$queryContext = new queryContext();
$queryContext->parseQueryString($_GET);

if($queryContext->subjPropId == null){
    echo json_encode(array("error" => "Must provide subject property id", "propId"=>null));
    exit;
}

$fullTable = new FullTable();
$fullTable->generateTableData($queryContext);

if($fullTable->getSubjCompArray() == null || sizeof($fullTable->getSubjCompArray()) == 1){
    echo json_encode(array("error"=>"No comps found", "propId"=> $queryContext->subjPropId), JSON_PRETTY_PRINT);
    exit;
}

echo generateJsonRows($fullTable,$queryContext->isEquityComp);
?>