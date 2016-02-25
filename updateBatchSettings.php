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

if(isset($_GET['subclassenabled']))
    $SUBCLASSENABLED='TRUE';
else
    $SUBCLASSENABLED='FALSE';

$CLASSRANGE=$_GET['range'];

if(isset($_GET['pctgoodenabled']))
    $PERCENTGOODENABLED='TRUE';
else
    $PERCENTGOODENABLED='FALSE';

$PERCENTGOODRANGE = $_GET['pctGoodRange'];

if(isset($_GET['netadjust']))
    $NET_ADJ_ENABLED='TRUE';
else
    $NET_ADJ_ENABLED='FALSE';

$NET_ADJ_AMOUNT = $_GET['netadjustamount'];

//$query="INSERT INTO BATCH_PROP_SETTINGS VALUES ('".$TRIMINDICATED."','".$MULTIHOOD."','".$INCLUDEVU."','".$INCLUDEMLS."','".$PREVYEAR."')";
$query="INSERT INTO `BATCH_PROP_SETTINGS` ( `TrimIndicated`, `MultiHood`, `IncludeVU`, `IncludeMLS`, `NumPrevYears`, `SqftRange`,
                                            `ClassRange`, `ClassRangeEnabled`, `PercentGood` , `PercentGoodEnabled`, `NetAdj`, `NetAdjEnabled`)
                                    VALUES('".$TRIMINDICATED."', '".$MULTIHOOD."', '".$INCLUDEVU."', '".$INCLUDEMLS."', ".$PREVYEAR.", ".$SQFTPCT.",".
                                            $CLASSRANGE.",". $SUBCLASSENABLED . "," . $PERCENTGOODRANGE . ",". $PERCENTGOODENABLED . "," . $NET_ADJ_AMOUNT . "," . $NET_ADJ_ENABLED .")";
$result=doSqlQuery($query);
if($result == TRUE){
    echo "Settings updated";
}
else
    echo "Error updating settings";
?>
<br>
<br><br><A HREF="batch_complete.php">Back</A>
</html>