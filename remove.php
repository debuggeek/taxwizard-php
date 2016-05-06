<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=MacRoman">
    <meta http-equiv="refresh" content="1;url=batch_complete.php" />
    <title>Remove PropID</title>
</head>
<body>
<?php
require_once "services/BatchService.php";
require_once 'library/queryContext.php';

use DebugGeek\TaxWizard\Services\BatchService;

$queryContext = new queryContext();
$queryContext->parseQueryString($_GET);

$batchService = new BatchService();

try {
    if($queryContext->subjPropId === 'ALL'){
        $batchService->deleteAllJobs();    
    } else {
        $batchService->deleteJob($queryContext->subjPropId);
    }
} catch (Exception $e){
    echo $e;
}

?>
<br>
<br><br><A HREF="batch_complete.php">Back</A>
</html>