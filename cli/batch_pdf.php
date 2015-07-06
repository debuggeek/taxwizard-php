<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "/home/cykoduck/public_html/debuggeek.com/taxtiger/");
include_once 'library/functions.php';
include_once 'library/functions_pdf.php';

$debug=false;
/**
 * For each non completed row in the batch_prop table
 * calculate the comp pdf and save it back to the pdfs column blob
 */
$TRIMINDICATED=false;
$MULTIHOOD=true;
$INCLUDEVU=true;
$PREVYEAR=1;
$SQFTPERCENT = .5;

$date = new DateTime();
echo "\n".$date->format('Y-m-d H:i:s') . " >> Starting Batch Processing\n";

$queueQuery = "SELECT * FROM BATCH_PROP_SETTINGS WHERE id=(SELECT max(id) FROM BATCH_PROP_SETTINGS)";
$result = doSqlQuery($queueQuery);

if(mysqli_num_rows($result) == 0){
    echo "\n Found no settings using defaults";
}
if(mysqli_num_rows($result) > 1){
    echo "\n Found multiple settings using first";
}
if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_array($result);
    $TRIMINDICATED= $row['TrimIndicated'] === "TRUE" ? true : false;
    $MULTIHOOD=$row['MultiHood']=== "TRUE" ? true : false;
    $INCLUDEVU=$row['IncludeVU']=== "TRUE" ? true : false;
    $INCLUDEMLS=$row['IncludeMLS']=== "TRUE" ? true : false;
    $PREVYEAR=$row['NumPrevYears'];
    $SQFTPERCENT=$row['SqftRange'];
    $SUBCLASSRANGE=$row['ClassRange'];
    $PERCENTGOODRANGE=$row['PercentGood'];
    $SUBCLASSENABLED=$row['ClassRangeEnabled'];
    $PERCENTGOODENABLED=$row['PercentGoodEnabled'];
    //REMEMBER if you add here you have to put into functions_pdf too
    $output = "Executing with settings: Trim=".strbool($TRIMINDICATED).
                                        " Multihoods=".strbool($MULTIHOOD).
                                        " VUs=".strbool($INCLUDEVU).
                                        " mls=".strbool($INCLUDEMLS).
                                        " years=".strval($PREVYEAR).
                                        " sqftRange=".strval($SQFTPERCENT).
                                        " subclassRange=".strval($SUBCLASSRANGE).
                                        " subclassEnabled=".strbool($SUBCLASSENABLED).
                                        " percentGoodRange=".strval($PERCENTGOODRANGE).
                                        " percentGoodEnabled=".strbool($PERCENTGOODENABLED);
    error_log("batch_pdf: ". $output);
    echo "\n".$output ."\n";
}
mysqli_free_result($result);

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