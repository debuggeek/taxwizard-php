<?php
include_once 'library/FullTable.php';
include_once 'library/functions.php';
include_once 'library/functions_pdf.php';
include_once 'library/presentation.php';

$debug = false;

global $INDICATEDVAL;

//Parse Inputs
$queryContext = new queryContext();
$queryContext->parseQueryString($_GET);

if($queryContext->subjPropId == ""){
    echo json_encode(array("error" => "Must provide subject property id", "propId"=>"0"));
    exit;
}

if($debug) error_log(var_dump($queryContext));

$fullTable = new FullTable();
$fullTable->generateTableData($queryContext);

if($fullTable->getSubjCompArray() == null || sizeof($fullTable->getSubjCompArray()) == 1){
    echo json_encode(array("error"=>"No comps found", "propId"=> $queryContext->subjPropId), JSON_PRETTY_PRINT);
    exit;
}

if(isset($_GET["pdf"])){
    $prop_pdfs = generatePropMultiPDF($queryContext);
    $multiPDF = $prop_pdfs["mPDF"];
    echo($multiPDF->Output($queryContext->subjPropId.'.pdf','I'));
} else {
    echo generateJsonRows($fullTable,$isEquityComp);
}

?>