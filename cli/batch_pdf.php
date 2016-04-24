<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "/home/cykoduck/public_html/debuggeek.com/fivestone/");
include_once 'library/functions.php';
include_once 'library/functions_pdf.php';

$debug=false;
/**
 * For each non completed row in the batch_prop table
 * calculate the comp pdf and save it back to the pdfs column blob
 */

$date = new DateTime();
echo "\n".$date->format('Y-m-d H:i:s') . " >> Starting Batch Processing\n";

$batchDAO = new BatchDAO($servername, $username, $password, $database);
$queryContext = $batchDAO->getBatchSettings();

//REMEMBER if you add here you have to put into functions_pdf too
$output = "Executing with settings: Trim=".strbool($queryContext->trimIndicated).
                                    " Multihoods=".strbool($queryContext->multiHood).
                                    " VUs=".strbool($queryContext->includeVu).
                                    " mls=".strbool($queryContext->includeMls).
                                    " years=".strval($queryContext->prevYear).
                                    " sqftRange=".strval($queryContext->sqftPercent).
                                    " subclassRange=".strval($queryContext->subClassRange).
                                    " subclassEnabled=".strbool($queryContext->subClassRangeEnabled).
                                    " percentGoodRange=".strval($queryContext->percentGoodRange).
                                    " percentGoodEnabled=".strbool($queryContext->percentGoodRangeEnabled).
                                    " netAdjEnabled=" . strbool($queryContext->netAdjustEnabled).
                                    " netAdjAmount=" . strval($queryContext->netAdjustAmount);
error_log("batch_pdf: ". $output);
echo "\n".$output ."\n";

//Query to check if any work to do
$queueQuery = "SELECT prop FROM BATCH_PROP WHERE completed='false'";
$result = doSqlQuery($queueQuery);

$completed = 0;
$uncompleted = 0;

if(mysqli_num_rows($result) > 0){
	while($row = mysqli_fetch_array($result)){
		$propid = $row['prop'];
		$date = new DateTime();
		echo $date->format('Y-m-d H:i:s') . " >> BatchPDF: Updating ".$propid;
        $queryContext->subjPropId = $propid;
		$retArray = generatePropMultiPDF($queryContext);
		if($retArray != null){
			//$multiPDF = new mPDF();
			$multiPDF = $retArray["mPDF"];
			$content = $multiPDF->Output('', 'S');
			$content = base64_encode($content);
			$updateQuery = "UPDATE BATCH_PROP SET completed='true', pdfs='".$content."',
																	prop_mktval='".$retArray[prop_mktvl]."',
																	Median_Sale5='".$retArray["medSale5"]."', 
																	Median_Sale10='".$retArray["medSale10"]."', 
																	Median_Sale15='".$retArray["medSale15"]."',
																	Median_Eq11='".$retArray["medEq11"]."' 
																	WHERE prop='".$propid."';";
			if($debugquery) error_log($updateQuery);
            $mysqli = sqldbconnect();
            if ($mysqli->query($updateQuery)){
				error_log("batch_pdf: Updated ".$propid."\n");
				$completed++;
                                echo "...Complete\n";
			}else{
				error_log("batch_pdf: Unable to update ".$propid."\n");
				$uncompleted++;
				echo "...ERROR: ".$mysqli->sqlstate." ".$mysqli->error."\n";
			}	
			$content = null;
            $mysqli->close();
		}
		if($debug==true) {
            error_log("batch_pdf: breaking while due to debug enabled");
            break;
        }
	}
	
}
mysqli_free_result($result);

$date = new DateTime();
echo $date->format('Y-m-d H:i:s') . " >> Completed Batch Processing.  Completed: ". $completed." Uncompleted: " . $uncompleted . "\n";
?>