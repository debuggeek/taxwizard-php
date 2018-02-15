<?php
include_once 'library/FullTable.php';
include_once 'library/functions.php';
include_once 'library/functions_pdf.php';
include_once 'library/presentation.php';
include_once 'library/responseContext.php';

$debug = false;

global $INDICATEDVAL;


/** @var queryContext $queryContext */
$queryContext = new queryContext();
/** @var responseContext $responseContext */
$responseContext = new responseContext();

//Parse Inputs
$json = file_get_contents('php://input');
$obj = json_decode($json);
$queryContext->parseQueryContextJson($obj);
$queryContext->responseCtx = $responseContext;

if($queryContext->subjPropId == ""){
    echo json_encode(array("error" => "Must provide subject property id", "propId"=>"0"));
    exit;
}

if($debug) error_log(var_dump($queryContext));

$fullTable = new FullTable();
try {
    $fullTable->generateTableData($queryContext);
} catch (Exception $e) {
    error_log("ERROR\tException in generating table: ".$e->getMessage());
    $responseContext->errors[] = $e->getMessage();
}

if($fullTable->getSubjCompArray() == null || sizeof($fullTable->getSubjCompArray()) == 1){
    echo json_encode(array("error"=>"No comps found for propId=".$queryContext->subjPropId, "propId"=> $queryContext->subjPropId), JSON_PRETTY_PRINT);
    exit;
}

if(isset($_GET["pdf"])){
    $prop_pdfs = generatePropMultiPDF($queryContext);
    $multiPDF = $prop_pdfs["mPDF"];
    echo($multiPDF->Output($queryContext->subjPropId.'.pdf','I'));
} else {
    header('Content-Type: application/json');
    echo generateJsonRows($fullTable,$isEquityComp, $responseContext);
}

?>