<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "/home1/cykoduck/public_html/debuggeek.com/fivestone/"); 

echo "\n--------------------------------------------------------------\n";
echo "Current Working dir: " . getcwd() . "\n";
chdir('/home1/cykoduck/public_html/debuggeek.com/fivestone/');
require_once 'library/functions.php';
include_once 'library/functions_pdf.php';
include_once 'library/BatchDAO.php';
include_once 'library/BatchJob.php';

$debug=false;
/**
 * For each non completed row in the batch_prop table
 * calculate the comp pdf and save it back to the pdfs column blob
 */
global $servername, $username, $password, $database;

/*
 * Parse commandline options
 */
$options = getopt("m::e::");
$mod = 1;
if($options['m'] != false){
	$mod = $options['m'];
}
if($options['e'] === false){
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', "stderr");
}


$date = new DateTime();
echo "\n".$date->format('Y-m-d H:i:s') . " >> Starting Batch Processing with mod $mod\n";
echo "\n Current Working dir:".getcwd();
$batchDAO = new BatchDAO($servername, $username, $password, $database);
$queryContext = $batchDAO->getBatchSettings();

echo "\nExecuting with settings: ";
var_dump($queryContext);

//Query to check if any work to do
$props = $batchDAO->getBatchJobs(false);

$completed = 0;
$uncompleted = 0;

foreach ($props as $prop){
	if(($prop % $mod) != 0){
        echo "\n". $date->format('Y-m-d H:i:s') ." >> Skipping $prop due to mod $mod\n";
		continue;
	}
	$job = new BatchJob();
	$job->propId = $prop;
	$date = new DateTime();
	echo "\n" .$date->format('Y-m-d H:i:s') . " >> BatchPDF: Updating ".$job->propId;
    error_log("Start Mem Usage: " . memory_get_usage());
	$queryContext->subjPropId = $prop;
	$retArray = generatePropMultiPDF($queryContext);
	if($retArray != null) {
		$multiPDF = $retArray["mPDF"];
		$content = $multiPDF->Output('', 'S');
		$content = base64_encode($content);
		$retArray['base64'] = $content;
		$job->parseArray($retArray);
		$job->batchStatus = true;
		if (!$batchDAO->updateBatchJob($job)) {
			error_log("batch_pdf: Unable to update " . $job->propid . "\n");
			$uncompleted++;
			echo "...ERROR\n";
		} else {
			error_log("batch_pdf: Updated " . $job->propid . "\n");
			$completed++;
			echo "...Complete\n";
		}
		$content = null;
		if ($debug == true) {
			error_log("batch_pdf: breaking while due to debug enabled");
			break;
		}
	} else {
		error_log("generatePropMultiPDF returned null array");
	}
}

$date = new DateTime();
echo $date->format('Y-m-d H:i:s') . " >> Completed Batch Processing.  Completed: ". $completed." Uncompleted: " . $uncompleted . "\n";
?>
