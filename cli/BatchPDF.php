<?php

require_once 'library/functions.php';
include_once 'library/functions_pdf.php';
include_once 'library/BatchDAO.php';
include_once 'library/BatchJob.php';


class BatchPDF{
    
    public static function run(){
        $debug = false;
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
        if ($options['m'] != false) {
            $mod = $options['m'];
        }
        if ($options['e'] === false) {
            ini_set('error_reporting', E_ALL & ~E_NOTICE);
            ini_set('display_errors', "stderr");
        }
    
    
        $date = new DateTime();
        echo "\n" . $date->format('Y-m-d H:i:s') . " >> Starting Batch Processing with mod $mod\n";
        echo "\n Current Working dir:" . getcwd();
        $batchDAO = new BatchDAO($servername, $username, $password, $database);
        $queryContext = $batchDAO->getBatchSettings();
    
        echo "\nExecuting with settings: ";
        var_dump($queryContext);
    
        //Query to check if any work to do
        $props = $batchDAO->getBatchJobsPropList(false);
    
        $completed = 0;
        $uncompleted = 0;
    
        foreach ($props as $prop) {
            if (($prop % $mod) != 0) {
                logStamp("Skipping $prop due to mod $mod");
                continue;
            }
            /** @var BatchJob $job */
            $job = $batchDAO->getBatchJob($prop);
            if ($job->batchStatus === 'true') {
                logStamp("Skipping $prop due to job already set to true");
                continue;
            }
            $date = new DateTime();
            logStamp("BatchPDF: Updating " . $job->propId);
            error_log("Start Mem Usage: " . memory_get_usage());
            $queryContext->subjPropId = $prop;
            $retArray = generatePropMultiPDF($queryContext);
            if ($retArray != null) {
                $multiPDF = $retArray["mPDF"];
                $content = $multiPDF->Output('', 'S');
                $content = base64_encode($content);
                $retArray['base64'] = $content;
                $job->parseArray($retArray);
                $job->batchStatus = true;
                if (!$batchDAO->updateBatchJob($job)) {
                    error_log("BatchPDF: Unable to update " . $job->propId . "\n");
                    $uncompleted++;
                    logStamp("ERROR updating $job->propId");
                } else {
                    error_log("BatchPDF: Updated " . $job->propId . "\n");
                    $completed++;
                    logStamp("BatchPDF: COMPLETED $job->propId");
                }
                $content = null;
                if ($debug == true) {
                    error_log("BatchPDF: breaking while due to debug enabled");
                    break;
                }
            } else {
                error_log("generatePropMultiPDF returned null array");
            }
        }
    
        logStamp("Completed Batch Processing.  Completed: " . $completed . " Uncompleted: " . $uncompleted);
    }
}

/**
 * @param String logString
 */
function logStamp($logString){
	$date = new DateTime();
	echo $date->format('Y-m-d H:i:s') . " >> " . $logString . "\n";
}

?>
