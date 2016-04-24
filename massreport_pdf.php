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
$queryContext = new queryContext();
$queryContext->parseQueryString($_GET);

if($queryContext->subjPropId != "")	$abort = false;

if($abort){
	echo "<p>Please enter a value</p>";
	exit;
}

$prop_pdfs = generatePropMultiPDF($queryContext);
$prop_pdfs->Output($queryContext->subjPropId.'.pdf','I');

?>