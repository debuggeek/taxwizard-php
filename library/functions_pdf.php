<?php
include_once 'defines.php';
include_once 'presentation.php';
include_once 'functions.php';
include_once 'FullTable.php';
include_once __DIR__ .'/mpdf6/mpdf.php';


function strbool($value)
{
    return $value ? 'true' : 'false';
}

/**
 * Retrieves a pdf for a property with a Sales 5, Sales 10 and Equity 11 report
 * @param queryContext $queryContext
 * @return Array with mPDF,medSale10,medSale5,medEq11,prop_mktvl
 * @throws MpdfException
 * @throws Exception
 */
function generatePropMultiPDF($queryContext)
{
    $_SESSION = array();
    $retArray = array();

    $mpdf = new mPDF('c', 'A4-L');
    $mpdf->SetMargins(1, 1, 1);
    // LOAD a stylesheet
    $stylesheet = file_get_contents('default_pdf.css');
    $mpdf->WriteHTML($stylesheet, 1);    // The parameter 1 tells that this is css/style only and no body/html/text

    //Get Sales Comps
    $fullTable = new FullTable();
    $queryContext->compsToDisplay = 100;
    $queryContext->isEquityComp = false;
    $fullTable->generateTableData($queryContext);
    $retArray['totalSalesComps'] = $fullTable->getNumComp();
    $property = $fullTable->getSubjectProp();
    $retArray["prop_mktvl"] = $property->mMarketVal;
    $retArray["compsFound"] = false;

    if ($fullTable->getSubjCompArray() !== null) {
        $retArray["compsFound"] = true;

        //Generate Sales 15
        $fullTable = $fullTable->trimTo(16);
		$retArray["medSale15"] = $fullTable->getMedianVal();
		$html15 = returnJsonBasedHTMLTable($fullTable, $queryContext->isEquityComp, $queryContext->responseCtx);
		error_log("Post Sales 15 JSON Mem Usage: " . memory_get_usage());

		$mpdf->WriteHTML($html15, 2);

		//Generate Sales 10
		$fullTable10 = null;
		if ($fullTable->getNumComp() >= 10) {
			//Take the first 10 comps of the 15 + the subj
			$fullTable10 = $fullTable->trimTo(11);
			$retArray["medSale10"] = $fullTable10->getMedianVal();
			$html10 = returnJsonBasedHTMLTable($fullTable10, $queryContext->isEquityComp, $queryContext->responseCtx);
			$mpdf->WriteHTML($html10, 2);
		} else {
			//Fanagle the numbers since we didn't have more then 10
			$retArray["medSale10"] = $retArray["medSale15"];;
			$retArray["medSale15"] = null;
		}

		//Generate Sales 5
		$fullTable5 = null;
		if ($fullTable->getNumComp() >= 6) {
			$fullTable5 = $fullTable->trimTo(6);
			$retArray["medSale5"] = $fullTable5->getMedianVal();
			$htmlEq = returnJsonBasedHTMLTable($fullTable5, $queryContext->isEquityComp, $queryContext->responseCtx);
			$mpdf->AddPage();
			$mpdf->WriteHTML($htmlEq);
		} else {
			//Fanagle the numbers since we didn't have more then 5
			$retArray["medSale5"] = $retArray["medSale10"];
			$retArray["medSale10"] = null;
		}
	} else {
        $retArray['totalSalesComps'] = 0;
    	error_log("No sales comps were found");
    }
	error_log("Post Sales PDF Mem Usage: " . memory_get_usage());

    //Generate Equity 10 comps
	$queryContext->compsToDisplay = 11;
	$queryContext->isEquityComp = true;
	$fullTableEq = new FullTable();
	$fullTableEq->generateTableData($queryContext);
	if($fullTableEq->getSubjCompArray() != null) {
        $retArray["compsFound"] = true;
        $retArray["medEq11"] = $fullTableEq->getMedianVal();
        $htmlEq = returnJsonBasedHTMLTable($fullTableEq, $queryContext->isEquityComp, $queryContext->responseCtx);
        $mpdf->AddPage();
        $mpdf->WriteHTML($htmlEq);
        error_log("Post Equity PDF Mem Usage: " . memory_get_usage());
        $retArray["mPDF"] = $mpdf;
    } else {
        $retArray['totalEquityComps'] = 0;
        error_log("No equity comps were found");
	}
	return $retArray;
}
?>
