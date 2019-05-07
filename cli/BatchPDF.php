<?php

require_once 'library/functions.php';
include_once 'library/functions_pdf.php';
include_once 'library/BatchDAO.php';
include_once 'library/BatchJob.php';
include_once 'library/responseContext.php';


class BatchPDF{
    
    public static function run(){
        /**
         * For each non completed row in the batch_prop table
         * calculate the comp pdf and save it back to the pdfs column blob
         */
        global $servername, $username, $password, $database, $production;
    
        /*
         * Parse commandline options
         */
        $options = getopt("m::e::t::");
        $mod = 1;
        if ($options['m'] != false) {
            $mod = $options['m'];
        }
        if ($options['e'] === false) {
            ini_set('error_reporting', E_ALL & ~E_NOTICE);
            ini_set('display_errors', "stderr");
        }
    
    
        $date = new DateTime();
        logStamp("Starting Batch Processing with mod $mod\n");
        logStamp("Current Working dir:" . getcwd());
        $batchDAO = new BatchDAO($servername, $username, $password, $database, $production);
        logStamp("Getting Batch settings");
        $queryContext = $batchDAO->getBatchSettings();
        $queryContext->responseCtx = new responseContext();

        if ($options['t'] === false){
            $queryContext->traceComps = true;
            var_dump($queryContext);
        }

        logStamp("Checking for work");
        //Query to check if any work to do
        $props = $batchDAO->getBatchJobsPropList(false);
    
        $completed = 0;
        $errored = 0;

        if(count($props) == 0){
            logStamp("\nNo properties found to process");
            return;
        }

        echo "\nExecuting with settings: ";
        var_dump($queryContext);

        try {
            foreach ($props as $prop) {
                try {
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
                    logStamp("BatchPDF: Updating " . $job->propId);
                    error_log("Start Mem Usage: " . memory_get_usage());
                    $queryContext->subjPropId = $prop;
                    if($queryContext->compsToDisplay <= 10){
                        //2019 change
                        $retArray = generateSinglePropPDF($queryContext);
                    } else {
                        error_log("More then 10 comps... using classic pdf");
                        $retArray = generatePropMultiPDF($queryContext);
                    }
                    if ($retArray == null) {
                        throw new Exception("retArray came back null");
                    }
                    if ($retArray["compsFound"] == true) {
                        $multiPDF = $retArray["mPDF"];
                        if($multiPDF == null){
                            error_log("ERROR: generatePropMultiPDF didn't return pdf");
                            error_log("Got : " + implode(",", $retArray));
                        } else {
                            $content = $multiPDF->Output('', 'S');
                            $content = base64_encode($content);
                            $retArray['base64'] = $content;
                            $retArray['mPDF'] = null;
                            $job->parseArray($retArray);
                            $job->batchStatus = true;
                            $content = null;
                        }
                    } else {
                        $job->batchStatus = true;
                        $job->errorsIn = "No comps found";
                    }
                    $batchDAO->updateBatchJob($job);
                    error_log("BatchPDF: Updated " . $job->propId . "\n");
                    $completed++;
                    logStamp("BatchPDF: COMPLETED $job->propId : total sales=$job->totalSalesComps : $job->errorsIn");
                } catch (Exception $e) {
                    $errored++;
                    $job->batchStatus = true;
                    $job->errorsIn = $e->getMessage();
                    error_log("ERROR\tBatchPDF>>>" . $e->getMessage());
                    $batchDAO->updateBatchJob($job);
                    logStamp("BatchPDF: ERRORED $job->propId : $job->errorsIn");
                }
            }
        } catch (Exception $e){
            error_log("ERROR\tBatchPDF>>> SHOULDN'T GET HERE:" . $e->getMessage());
        }
    
        logStamp("Completed Batch Processing.  Completed: " . $completed . " Errored: " . $errored);
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
