<?php
include_once 'defines.php';
include_once 'presentation.php';
include_once 'functions.php';
include_once 'FullTable.php';
include_once 'MPDF56/mpdf.php';

function strbool($value)
{
    return $value ? 'true' : 'false';
}

/**
 * Retrieves a pdf for a property with a Sales 5, Sales 10 and Equity 11 report
 * @param queryContext $queryContext
 * @return Array with mPDF,medSale10,medSale5,medEq11,prop_mktvl
 */
function generatePropMultiPDF($queryContext){
	$_SESSION = array();
	$retArray = array();

	$mpdf=new mPDF('c','A4-L');
	$mpdf->SetMargins(1,1,1);
	// LOAD a stylesheet
	$stylesheet = file_get_contents('default_pdf.css');
	$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

	$fullTable = new FullTable();
	//Generate Sales 15
	$queryContext->compsToDisplay = 1000;
	$queryContext->isEquityComp = false;
	$fullTable->generateTableData($queryContext);
	$retArray['totalSalesComps'] = $fullTable->getNumComp();
	$property = $fullTable->getSubjectProp();
	$retArray["prop_mktvl"] = $property->mMarketVal;

	if ($fullTable->getNumComp() == 0)
		$html15 = "No Sales Comps for ".$queryContext->subjPropId;
	else{
		$retArray["medSale15"] = $fullTable->getMedianVal();
		$html15 = returnJsonBasedHTMLTable($fullTable, $queryContext->isEquityComp);
	}
	$mpdf->WriteHTML($html15,2);

	//Generate Sales 10
	$fullTable10 = null;
	if ($fullTable->getNumComp() >= 10){
        //Take the first 10 comps of the 15 + the subj
		$fullTable10 = $fullTable->trimTo(11);
		$retArray["medSale10"] = $fullTable10->getMedianVal();
		$html10 = returnJsonBasedHTMLTable($fullTable10,$queryContext->isEquityComp);
		$mpdf->WriteHTML($html10,2);
	}
	else{
		//Fanagle the numbers since we didn't have more then 10
		$retArray["medSale10"] = $retArray["medSale15"];;
		$retArray["medSale15"] = null;
	}
	
	//Generate Sales 5
	$fullTable5 = null;
	if ($fullTable->getNumComp() >= 6){
		$fullTable5 = $fullTable10->trimTo(6);
		$retArray["medSale5"] = $fullTable5->getMedianVal();
		$htmlEq = returnJsonBasedHTMLTable($fullTable5,$queryContext->isEquityComp);
		$mpdf->AddPage();
		$mpdf->WriteHTML($htmlEq);
	}
	else{
		//Fanagle the numbers since we didn't have more then 5
		$retArray["medSale5"] = $retArray["medSale10"];
		$retArray["medSale10"] = null;
	}

	//Generate Equity 10
	$queryContext->compsToDisplay = 11;
	$queryContext->isEquityComp = true;
	$fullTableEq = new FullTable();
	$fullTableEq->generateTableData($queryContext);
	$retArray["medEq11"] = $fullTableEq->getMedianVal();
	$htmlEq = returnJsonBasedHTMLTable($fullTableEq,$queryContext->isEquityComp);
	$mpdf->AddPage();
	$mpdf->WriteHTML($htmlEq);
	$retArray["mPDF"] = $mpdf;
	return $retArray;
}
?>
