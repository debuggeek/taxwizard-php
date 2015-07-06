<?php
include_once 'defines.php';
include_once "MPDF56/mpdf.php";
include_once 'presentation.php';
include_once 'functions.php';

function strbool($value)
{
    return $value ? 'true' : 'false';
}

/**
 * Retrieves a pdf for a property with a Sales 5, Sales 10 and Equity 11 report
 * @param String $propid
 * @return Array with mPDF,medSale10,medSale5,medEq11,prop_mktvl
 */
function generatePropMultiPDF($propid){
	global $MEANVAL,$MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT;
	$_SESSION = array();
	$retArray = array();
	
	$mpdf=new mPDF('c','A4-L',"","","1","1","1","1");
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list
	// LOAD a stylesheet
	$stylesheet = file_get_contents('default_pdf.css', FILE_USE_INCLUDE_PATH);
	$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

	$property = getSubjProperty($propid);
	$retArray["prop_mktvl"] = $property->mMarketVal;
	//Generate Sales 15
	$subjcomparray15 = generatePdfArray($property,false,15);
	if ($subjcomparray15 == null)
		$html15 = "No Sales Comps for ".$propid;
	else{
		$_SESSION[$MEANVAL[0]] = getMeanVal($subjcomparray15);
		$_SESSION[$MEANVALSQFT[0]] = getMeanValSqft($subjcomparray15);
		$_SESSION[$MEDIANVAL[0]] = getMedianVal($subjcomparray15);
		$_SESSION[$MEDIANVALSQFT[0]] = getMedianValSqft($subjcomparray15);
		$retArray["medSale15"] = $_SESSION[$MEDIANVAL[0]];
		$html15 = returnGenericTable($subjcomparray15,false);
	}
	$mpdf->WriteHTML($html15,2);

	//Generate Sales 10
	//$subjcomparray10 = generateArray($property,false);
	$subjcomparray10 = null;
	if (sizeof($subjcomparray15) >= 10){
        //Take the first 10 comps of the 15 + the subj
		$subjcomparray10 = array_slice($subjcomparray15, 0,11);
		$_SESSION[$MEANVAL[0]] = getMeanVal($subjcomparray10);
		$_SESSION[$MEANVALSQFT[0]] = getMeanValSqft($subjcomparray10);
		$_SESSION[$MEDIANVAL[0]] = getMedianVal($subjcomparray10);
		$_SESSION[$MEDIANVALSQFT[0]] = getMedianValSqft($subjcomparray10);
		$retArray["medSale10"] = $_SESSION[$MEDIANVAL[0]];
		$html10 = returnGenericTable($subjcomparray10,false);
		$mpdf->WriteHTML($html10,2);
	}
	else{
		//Fanagle the numbers since we didn't have more then 5
		$retArray["medSale10"] = $retArray["medSale15"];;
		$retArray["medSale15"] = null;
	}
	
	
	//Generate Sales 5
	if(sizeof($subjcomparray10) >=6){
		$subjcomparray5 = array_slice($subjcomparray15, 0,6);
		$_SESSION[$MEANVAL[0]] = getMeanVal($subjcomparray5);
		$_SESSION[$MEANVALSQFT[0]] = getMeanValSqft($subjcomparray5);
		$_SESSION[$MEDIANVAL[0]] = getMedianVal($subjcomparray5);
		$_SESSION[$MEDIANVALSQFT[0]] = getMedianValSqft($subjcomparray5);
		$retArray["medSale5"] = $_SESSION[$MEDIANVAL[0]];
		$htmlEq = returnGenericTable($subjcomparray5,false);
		$mpdf->AddPage();
		$mpdf->WriteHTML($htmlEq);
	}
	else{
		//Fanagle the numbers since we didn't have more then 5
		$retArray["medSale5"] = $retArray["medSale10"];
		$retArray["medSale10"] = null;
	}

	//Generate Equity 10
	$subjcomparrayEq = generatePdfArray($property,true);
	$_SESSION[$MEANVAL[0]] = getMeanVal($subjcomparrayEq);
	$_SESSION[$MEANVALSQFT[0]] = getMeanValSqft($subjcomparrayEq);
	$_SESSION[$MEDIANVAL[0]] = getMedianVal($subjcomparrayEq);
	$_SESSION[$MEDIANVALSQFT[0]] = getMedianValSqft($subjcomparrayEq);
	$retArray["medEq11"] = $_SESSION[$MEDIANVAL[0]];
	$htmlEq = returnGenericTable($subjcomparrayEq,true);
	$mpdf->AddPage();
	$mpdf->WriteHTML($htmlEq);
	$retArray["mPDF"] = $mpdf;
	return $retArray;
}


function generatePdfArray(propertyClass $property,$eqComp,$numComps=10){
    global $TRIMINDICATED,$MULTIHOOD,$INCLUDEVU,$PREVYEAR,$INCLUDEMLS,$SQFTPERCENT,
           $SUBCLASSRANGE,$PERCENTGOODRANGE, $SUBCLASSENABLED, $PERCENTGOODENABLED,
           $NET_ADJ_ENABLED,$NET_ADJ_AMOUNT;

    $COMPSTODISPLAY = $numComps;
    if($eqComp)
        $COMPSTODISPLAY = 11;

    $queryContext = new queryContext();
    $queryContext->trimIndicated = $TRIMINDICATED;
    $queryContext->multiHood = $MULTIHOOD;
    $queryContext->includeVu = $INCLUDEVU;
    $queryContext->prevYear = $PREVYEAR;
    $queryContext->includeMls = $INCLUDEMLS;
    $queryContext->sqftPercent = $SQFTPERCENT;
    $queryContext->subClassRange = $SUBCLASSRANGE;
    $queryContext->percentGoodRange = $PERCENTGOODRANGE;
    $queryContext->compsToDisplay = $COMPSTODISPLAY;
    $queryContext->isEquityComp = $eqComp;
    $queryContext->subClassRangeEnabled = $SUBCLASSENABLED;
    $queryContext->percentGoodRangeEnabled = $PERCENTGOODENABLED;
    $queryContext->netAdjustEnabled = $NET_ADJ_ENABLED;
    $queryContext->netAdjustAmount = $NET_ADJ_AMOUNT;

    return generateArray($property,$queryContext);
}
?>