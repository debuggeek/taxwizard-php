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

function setupPDF(){
    $mpdf = new mPDF('c', 'A4-L');
    $mpdf->SetMargins(1, 1, 1);
    // LOAD a stylesheet
    $stylesheet = file_get_contents('default_pdf.css');
    $mpdf->WriteHTML($stylesheet, 1);    // The parameter 1 tells that this is css/style only and no body/html/text

    return $mpdf;
}

/**
 * @param queryContext $queryContext
 * @param array $retArray
 * @return FullTable
 * @throws Exception
 */
function setupFullTable($queryContext, &$retArray){
    $queryContext->validate();

    $_SESSION = array();

    //Get Sales Comps
    $fullTable = new FullTable();
    $fullTable->generateTableData($queryContext);
    $retArray['totalSalesComps'] = $fullTable->getTotalFilteredCompsFound();

    $property = $fullTable->getSubjectProp();
    $retArray["prop_mktvl"] = $property->mMarketVal;
    $retArray["compsFound"] = false;

    if($fullTable->getSubjCompArray() == null){
        $retArray['totalSalesComps'] = 0;
        error_log("No sales comps were found");
    } else {
        $retArray["compsFound"] = true;
    }

    return $fullTable;
}

/**
 * @param FullTable $fullTable
 * @param array $retArray
 */
function getReturnComps(&$fullTable, &$retArray){
    //2019 set up to the first 10 comps indicated values
    $compCount = 1;
    if($fullTable->getCompIndicatedValues() != null) {
        foreach ($fullTable->getCompIndicatedValues() as $value) {
            $compEntry = "Comp" . $compCount . "_IndicatedValue";
            $retArray[$compEntry] = $value;
            $compCount = $compCount + 1;
        }
    }
}
/**
 * Retrieves a pdf for a property with a Sales 10 report
 * @param queryContext $queryContext
 * @return array with mPDF,medSale10,medSale5,medEq11,prop_mktvl
 * @throws MpdfException
 * @throws Exception
 */
function generateSinglePropPDF($queryContext){
    //If we have comps so generate a single 10 PDF per issue 51 request
    $trim = 11;
    $retArray = array();
    $fullTable = setupFullTable($queryContext, $retArray);
    $mpdf = setupPDF();

    if($retArray["compsFound"] == false){
        return $retArray;
    }

    if($queryContext->compsToDisplay){
        //If a limit is set then use it...but we need to add the subj to the total
        $trim = $queryContext->compsToDisplay + 1;
    }

    $fullTable10 = $fullTable->trimTo($trim);
    $retArray["medSale10"] = $fullTable10->getMedianVal();
    $retArray["lowSale10"] = $fullTable10->getLowVal();
    $retArray["highSale10"] = $fullTable10->getHighVal();
    $retArray["compType"]  = $queryContext->compType;
    $html10 = returnJsonBasedHTMLTable($fullTable10, $queryContext->isEquityComp, $queryContext->responseCtx);
    $mpdf->WriteHTML($html10, 2);

    getReturnComps($fullTable10, $retArray);

    //Add PDFs back to return array
    $retArray["mPDF"] = $mpdf;

	return $retArray;
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
    $retArray = array();
    $fullTable = setupFullTable($queryContext, $retArray);
    $mpdf = setupPDF();

    if ($fullTable->getSubjCompArray() !== null) {
        $retArray["compsFound"] = true;

        getReturnComps($fullTable10, $retArray);
        // Generate Sales All

        //Generate Sales 15
		if($fullTable->getNumComp() >= 15) {
            $fullTable = $fullTable->trimTo(16);
            $retArray["medSale15"] = $fullTable->getMedianVal();
            $retArray["lowSale15"] = $fullTable->getLowVal();
            $retArray["highSale15"] = $fullTable->getHighVal();
            $html15 = returnJsonBasedHTMLTable($fullTable, $queryContext->isEquityComp, $queryContext->responseCtx);
            error_log("Post Sales 15 JSON Mem Usage: " . memory_get_usage());

            $mpdf->WriteHTML($html15, 2);
        }

		//Generate Sales 10
		$fullTable10 = null;
		if ($fullTable->getNumComp() >= 10) {
			//Take the first 10 comps of the 15 + the subj
			$fullTable10 = $fullTable->trimTo(11);
			$retArray["medSale10"] = $fullTable10->getMedianVal();
            $retArray["lowSale10"] = $fullTable10->getLowVal();
            $retArray["highSale10"] = $fullTable10->getHighVal();
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
            $retArray["lowSale5"] = $fullTable5->getLowVal();
            $retArray["highSale5"] = $fullTable5->getHighVal();
			$htmlEq = returnJsonBasedHTMLTable($fullTable5, $queryContext->isEquityComp, $queryContext->responseCtx);
			$mpdf->AddPage();
			$mpdf->WriteHTML($htmlEq);
		} else {
			//Fanagle the numbers since we didn't have more then 5
            $retArray["medSale5"] = $fullTable->getMedianVal();
            $retArray["lowSale5"] = $fullTable->getLowVal();
            $retArray["highSale5"] = $fullTable->getHighVal();
            $htmlEq = returnJsonBasedHTMLTable($fullTable, $queryContext->isEquityComp, $queryContext->responseCtx);
            $mpdf->AddPage();
            $mpdf->WriteHTML($htmlEq);
		}
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
    } else {
        $retArray['totalEquityComps'] = 0;
        error_log("No equity comps were found");
	}

	//Add PDFs back to return array
    $retArray["mPDF"] = $mpdf;

	return $retArray;
}
?>
