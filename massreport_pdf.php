<?php
session_start();
include_once 'library/propertyClass.php';
include_once 'library/functions.php';
include_once 'library/functions_pdf.php';
include_once 'library/presentation.php';
include_once 'library/batch.php';


$debug = false;
$COMPSTODISPLAY = 100;
$LIMIT=NULL;
$TRIMINDICATED = false;
$INCLUDEMLS = false;

global $INDICATEDVAL;

//Treat like equity so we use market val and not sales price
$isEquityComp = true;

//Parse Inputs
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
}

if($propid != "")	$abort = false;

if($abort){
	echo "<p>Please enter a value</p>";
	exit;
}

$prop_pdfs = generatePropMultiPDF($propid);
$prop_pdfs->Output($propid.'.pdf','I');

?>