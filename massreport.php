<?php
session_start();
include_once 'library/propertyClass.php';
include_once 'library/functions.php';
include_once 'library/functions_pdf.php';
include_once 'library/presentation.php';
include_once 'MPDF56/mpdf.php';

$debug = false;
$COMPSTODISPLAY = 100;
$LIMIT=NULL;
$TRIMINDICATED = false;
$INCLUDEMLS = false;
$MULTIHOOD = false;
$INCLUDEVU = false;
$PREVYEAR = 1;  //By default go back 1 cal year for Sales
$SQFTPERCENT = .75;

global $INDICATEDVAL;

//Treat like equity so we use market val and not sales price
$isEquityComp = true;

//Parse Inputs
if(isset($_GET['multiyear'])){
    $PREVYEAR = $_GET['multiyear'];
}

for($i=0; $i < $c; $i++)
{
	if($keys[$i] == 'propid'){
		$targ = $_GET['propid'];
		$targ = trim($targ);
		$propid = $targ;
		$targ = NULL;
	}
	if($keys[$i] == 'display'){
		$targ = $_GET['display'];
		$targ = trim($targ);
		$COMPSTODISPLAY = intval($targ);
		$targ = NULL;
	}
	if($keys[$i] == 'style'){
		$targ = $_GET['style'];
		$targ = trim($targ);
		if($targ == 'sales')
			$isEquityComp = false;
		if($targ == 'on')
			
		$targ = NULL;
	}
	if($keys[$i] == "trimindicated"){
		$targ = $_GET['trimindicated'];
		$targ = trim($targ);
		if($targ == "on")
			$TRIMINDICATED = true;
		$targ = NULL;
	}
	if($keys[$i] == "includemls"){
		$targ = $_GET['includemls'];
		$targ = trim($targ);
		if($targ == "on")
			$INCLUDEMLS = true;	
		$targ = NULL;
	}
	if($keys[$i] == "limit"){
		$targ = $_GET['limit'];
		$targ = trim($targ);
		if($targ > 0)
			$COMPSTODISPLAY = $targ;
		$targ = NULL;
	}
	if($keys[$i] == "multihood"){
		$targ = $_GET['multihood'];
		$targ = trim($targ);
		if($targ == "on")
			$MULTIHOOD = true;
		$targ = NULL;
	}
    if($keys[$i] == "includevu"){
        $targ = $_GET['includevu'];
        $targ = trim($targ);
        if($targ == "on")
            $INCLUDEVU = true;
        $targ = NULL;
    }
}

if(isset($_GET['sqftPct'])){
    $SQFTPERCENT = trim($_GET['sqftPct']);
}

if($propid != "")	$abort = false;

if($abort){
	echo "<p>Please enter a value</p>";
	exit;
}

$property = getSubjProperty($propid);

error_log("Finding best comps for ".$propid);


/*
 * This has to be kept in sync with functions_pdf.php
 * Should be merged
 */

$compsarray = findBestComps($property,$isEquityComp,$SQFTPERCENT,$TRIMINDICATED,$MULTIHOOD,$INCLUDEVU,$PREVYEAR);

if(sizeof($compsarray) == 0){
    error_log("massreport: no comps found for ".$propid);
    return returnNoHits($propid);
}

if(!$INCLUDEMLS){
    $compsarray = array_filter($compsarray,"isNotMLS");
}

if(sizeof($compsarray) == 0){
    error_log("massreport: no comps found after MLS Sort for ".$propid);
    return returnNoHits($propid);
}

error_log("massreport: found ".sizeof($compsarray)." comp(s) for ".$propid);

usort($compsarray,"cmpProp");

$comp_min = MIN($COMPSTODISPLAY,count($compsarray));
$subjcomparray = array();
$subjcomparray[0] = $property;

for($i=0; $i < $comp_min; $i++)
{
    $subjcomparray[$i+1] = $compsarray[$i];
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