<?php
session_start();
include_once 'library/propertyClass.php';
include_once 'library/functions.php';
include_once 'library/functions_pdf.php';
include_once 'library/presentation.php';
include_once 'MPDF56/mpdf.php';

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

$property = getSubjProperty($queryContext->subjPropId);

error_log("Finding best comps for ".$property->getPropID());

$subjcomparray = generateArray($property, $queryContext);

if(count($queryContext->excludes) > 0){
    //Save off since it might go down
    $startCount = count($subjcomparray);
    for($i = 0 ; $i < $startCount; $i++){
        /* @var propertyClass $property */
        $property = $subjcomparray[$i];
        if(in_array($property->getPropID(), $queryContext->excludes)){
            error_log("Removing ".$property->getPropID()." from comp results due to being in excludes");
            unset($subjcomparray[$i]);
        }
    }
    //Re-index the array so we don't have gaps
    $subjcomparray = array_values($subjcomparray);
}

if($subjcomparray == null || sizeof($subjcomparray) == 1){
    //returnNoHits($property->getPropID());
    //echo "{'error':'No comps found'}";
    echo json_encode(array("error"=>"No comps found", "propId"=>$property->getPropID()), JSON_PRETTY_PRINT);
    exit;
}

$fullTable = array();
$fullTable["subjComps"] = $subjcomparray;

$fullTable["meanVal"] = getMeanVal($subjcomparray);
$fullTable["meanValSqft"] = getMeanValSqft($subjcomparray);
$fullTable["medianVal"]= getMedianVal($subjcomparray);
$fullTable["medianValSqft"] = getMedianValSqft($subjcomparray);

if(isset($_GET["pdf"])){
    $prop_pdfs = generatePropMultiPDF($propid);
    $multiPDF = $prop_pdfs["mPDF"];
    echo($multiPDF->Output($propid.'.pdf','I'));
} else {
    echo generateJsonRows($fullTable,$isEquityComp);
}

?>