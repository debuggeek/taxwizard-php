<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "/home/cykoduck/public_html/debuggeek.com/taxtiger/");
include_once 'functions.php';
include_once 'functions_pdf.php';

$debug=false;
/**
 * For each non completed row in the batch_prop table
 * calculate the comp pdf and save it back to the pdfs column blob
 */

$date = new DateTime();
echo "\n".$date->format('Y-m-d H:i:s') . " >> Starting Batch Processing\n";
 
//Query to check if any work to do
$queueQuery = "SELECT prop FROM BATCH_PROP WHERE completed='false'";
$result = executeQuery($queueQuery);

$completed = 0;
$uncompleted = 0;

if(mysql_numrows($result) > 0){
	while($row = mysql_fetch_array($result)){
		$propid = $row['prop'];
		$date = new DateTime();
		echo $date->format('Y-m-d H:i:s') . " >> BatchPDF: Updating ".$propid;
		$retArray = generatePropMultiPDF($propid);
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
																	WHERE prop='".$propid."'";
			//error_log($updateQuery);
			$link = sqldbconnect();
			$result2=mysql_query($updateQuery) or die(error_log("Unable to update : " . mysql_error()));		
			if(mysql_affected_rows($link)==1){
				error_log("BatchPDF: Updated ".$propid."\n");
				$completed++;
                                echo "...Complete\n";
			}else{
				error_log("BatchPDF: Unable to update"."\n");
				$uncompleted++;
				echo "...ERROR\n";
			}	
			$content = null;
			mysql_close($link);
		}
		if($debug==true)
			break;
	}
	
}
mysql_free_result($result);

$date = new DateTime();
echo $date->format('Y-m-d H:i:s') . " >> Completed Batch Processing.  Completed: ". $completed." Uncompleted: " . $uncompleted . "\n";
?>