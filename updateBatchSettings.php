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

$propid = null;

//Parse Inputs
if(isset($_GET['trimindicated']))
    $TRIMINDICATED='TRUE';
else
    $TRIMINDICATED='FALSE';

if(isset($_GET['multihood']))
    $MULTIHOOD='TRUE';
else
    $MULTIHOOD='FALSE';

if(isset($_GET['includevu']))
    $INCLUDEVU='TRUE';
else
    $INCLUDEVU='FALSE';

if(isset($_GET['includemls']))
    $INCLUDEMLS='TRUE';
else
    $INCLUDEMLS='FALSE';

$PREVYEAR=$_GET['multiyear'];

$SQFTPCT=$_GET['sqftPct'];

//$query="INSERT INTO BATCH_PROP_SETTINGS VALUES ('".$TRIMINDICATED."','".$MULTIHOOD."','".$INCLUDEVU."','".$INCLUDEMLS."','".$PREVYEAR."')";
$query="INSERT INTO `BATCH_PROP_SETTINGS` ( `TrimIndicated`, `MultiHood`, `IncludeVU`, `IncludeMLS`, `NumPrevYears`, `SqftRange`)
                                    VALUES('".$TRIMINDICATED."', '".$MULTIHOOD."', '".$INCLUDEVU."', '".$INCLUDEMLS."', ".$PREVYEAR.", ".$SQFTPCT.")";
$result=executeQuery($query);
if($result == TRUE){
    echo "Settings updated";
}
else
    echo "Error updating settings";
?>
<br>
<br><br><A HREF="batch_complete.php">Back</A>
</html>