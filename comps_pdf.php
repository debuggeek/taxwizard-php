<?php
session_start();
include_once 'library/presentation.php';
include_once 'library/batch.php';

$subj = "";
$complist = "";
$equity = false;
$abort = true;

//Parse Inputs
//  Sample: comps_pdf.php?subj=100867&complist=302915,283553,
for($i=0; $i < $c; $i++)
{
	if($keys[$i] == 'subj'){
		$targ = $_GET['subj'];
		$targ = trim($targ);
		$subj = $targ;
		$targ = NULL;
	}
	if($keys[$i] == 'complist'){
		$targ = $_GET['complist'];
		$targ = trim($targ);
		$complist = $targ;
		$targ = NULL;
	}
	if($keys[$i] == 'equity'){
		$targ = $_GET['equity'];
		$targ = trim($targ);
		$equity = $targ;
		$targ = NULL;
	}
}

if($subj != "")	$abort = false;
if($complist != "")	$abort = false;

if($abort){
	echo "<p>Please enter a value</p>";
	exit;
}

$complist=trim($complist,",");	
$propstrarray = explode(",",$complist);
array_unshift($propstrarray,$subj);

$propArray = array();
foreach($propstrarray as $propstr){
	$currProp = getProperty($propstr);
	if($propstr == $subj){
		$currProp->mSubj = true;
	}
	else{
		if($equity == false){
			//Get Sales info
			$currProp->mSaleDate = getSaleDate($currProp->mPropID);
			$currProp->mSalePrice = getSalePrice($currProp->mPropID);
		}
		calcDeltas($propArray[0],$currProp);
	}
	$propArray[] = $currProp;
}
$_SESSION[$MEANVAL[0]] = getMeanVal($propArray);
$_SESSION[$MEANVALSQFT[0]] = getMeanValSqft($propArray);
$_SESSION[$MEDIANVAL[0]] = getMedianVal($propArray);
$_SESSION[$MEDIANVALSQFT[0]] = getMedianValSqft($propArray);

$html = returnGenericTable($propArray,$equity);
generatePDF($html);
?>