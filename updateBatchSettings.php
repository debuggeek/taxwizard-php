<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=MacRoman">
    <meta http-equiv="refresh" content="2;url=batch_complete.php" />
    <title>Update Batch Settings</title>
</head>
<body>
<?php
include_once("library/functions.php");
include_once("library/BatchDAO.php");

global $servername,$username,$password,$database,$dbport,$production;

$propid = null;
$batchDAO = new BatchDAO($servername, $username, $password, $database,$production);

//Parse Inputs
$queryContext = new queryContext();
$queryContext->parseQueryString($_GET);

$result = $batchDAO->updateBatchSettings($queryContext);
if($result == TRUE){
    echo "Settings updated";
}
else
    echo "Error updating settings";
?>
<br>
<br><br><A HREF="batch_complete.php">Back</A>
</html>