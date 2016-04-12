<?php
session_start();
include_once 'library/propertyClass.php';
include_once 'library/functions.php';
include_once 'library/functions_pdf.php';
include_once 'library/presentation.php';
include_once 'MPDF56/mpdf.php';

$debug = false;

global $INDICATEDVAL;

$queryContext = new queryContext();

//Parse Inputs
if(isset($_GET['multiyear'])){
    $queryContext->prevYear = $_GET['multiyear'];
}

if(isset($_GET['display'])){
    $queryContext->compsToDisplay = intval(trim($_GET['display']));
}
if(isset($_GET['propid'])){
    $queryContext->subjPropId = trim($_GET['propid']);
}
if(isset($_GET['trimindicated'])){
    if (trim($_GET['trimindicated']) == 'on'){
        $queryContext->trimIndicated = true;
    }
}
if(isset($_GET['style'])){
    if (trim($_GET['style']) == 'sales'){
        $queryContext->isEquityComp = false;
    }
}
if(isset($_GET['includemls'])){
    if (trim($_GET['includemls']) == 'on'){
        $queryContext->includeMls = true;
    }
}
if(isset($_GET['multihood'])){
    if (trim($_GET['multihood']) == 'on'){
        $queryContext->multiHood = true;
    }
}

if(isset($_GET['includevu'])) {
    $queryContext->includeVu = trim($_GET['includevu']);
}

if(isset($_GET['sqftPct'])){
    $queryContext->sqftPercent = trim($_GET['sqftPct']);
}

if(isset($_GET['rangeEnabled'])){
    if(strcmp($_GET['rangeEnabled'],'on') == 0){
        $queryContext->subClassRangeEnabled = true;
        $queryContext->subClassRange = trim($_GET['range']);
    }
}

if(isset($_GET['pctGoodRangeEnabled'])){
    if(strcmp($_GET['pctGoodRangeEnabled'], 'on') ==0 ) {
        $queryContext->percentGoodRangeEnabled = true;
        $queryContext->percentGoodRange = trim($_GET['pctGoodRange']);
    }
}

if(isset($_GET['netadjust'])){
    if(strcmp($_GET['netadjust'], 'on') ==0 ) {
        $queryContext->netAdjustEnabled = true;
        $queryContext->netAdjustAmount = trim($_GET['netAdjustAmt']);
    }
}

if(isset($_GET['exclude'])){
    $excludeStrList = trim($_GET['exclude']);
    $queryContext->excludes = explode('_',$excludeStrList);
}

if($queryContext->subjPropId == ""){
	echo "<p>Please enter a value</p>";
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
    returnNoHits($property->getPropID());
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