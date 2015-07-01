<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=MacRoman">
    <meta http-equiv="refresh" content="1;url=batch_complete.php" />
<title>Reset PropID</title>
</head>
    <body>
    <?php
include_once("library/functions.php");

$propid = null;

//Parse Inputs
for($i=0; $i < $c; $i++)
{
	if($keys[$i] == 'subj'){
		$targ = $_GET['subj'];
		$targ = trim($targ);
		$propid = $targ;
		$targ = NULL;
	}
}
if($propid == "ALL")
	$query="UPDATE BATCH_PROP SET completed='false'";
else
 	$query="UPDATE BATCH_PROP SET completed='false' WHERE prop='".$propid."'";

$result=doSqlQuery($query);
if($result == TRUE){
	//$query="INSERT INTO BATCH_PROP (prop) VALUES ('".$propid."')";
	//$result=executeQuery($query);
	//if($result == TRUE)
		echo "$propid reset";
	//else
	//	echo "Error re-inserting $propid";
}
else
	echo "Error resetting $propid";

//Execute the batch pdf
if($production==false)
	$phpCmd = "/Applications/MAMP/bin/php/php5.2.17/bin/php ";
else
	$phpCmd = "php-cli ";
$filename = "./cli/batch_pdf.php";
$output = shell_exec("$phpCmd $filename >error_log 2>&1 &");
?>
<br>
	<br><br><A HREF="batch_complete.php">Back</A>
</html>