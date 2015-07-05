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

if(isset($_GET['classrange'])){
    if(strcmp($_GET['classrange'],'on') == 0){
        $queryContext->subClassRange = trim($_GET['range']);
    }
}

if(isset($_GET['pctGoodRange'])){
    $queryContext->percentGoodRange = trim($_GET['pctGoodRange']);
}

if($queryContext->subjPropId == ""){
	echo "<p>Please enter a value</p>";
	exit;
}

if($debug) error_log(var_dump($queryContext));

$property = getSubjProperty($queryContext->subjPropId);

error_log("Finding best comps for ".$property->mPropID);

$subjcomparray = generateArray($property, $queryContext);

if($subjcomparray == null){
    returnNoHits($property->mPropID);
}

$_SESSION[$MEANVAL[0]] = getMeanVal($subjcomparray);
$_SESSION[$MEANVALSQFT[0]] = getMeanValSqft($subjcomparray);
$_SESSION[$MEDIANVAL[0]] = getMedianVal($subjcomparray);
$_SESSION[$MEDIANVALSQFT[0]] = getMedianValSqft($subjcomparray);

if(isset($_GET["pdf"])){
    $prop_pdfs = generatePropMultiPDF($propid);
    $multiPDF = $prop_pdfs["mPDF"];
    echo($multiPDF->Output($propid.'.pdf','I'));
} else {
    createGenericTable($subjcomparray,$isEquityComp);
}

?>