<?php
include_once("library/functions.php");
$propid = NULL;

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
$finalpdf = null;

if($propid == "ALL"){
	$query="SELECT * FROM BATCH_PROP WHERE completed='true'";
	$result=doSqlQuery($query);
	$num=mysqli_num_rows($result);
	error_log("download_pdf>>Found ".$num." property ids.");
	
	$file = tempnam("tmp", "zip");
	$zip = new ZipArchive();
	$res = $zip->open($file, ZipArchive::OVERWRITE);
	if ($res === TRUE) {
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			$zip->addFromString($row['prop'].'.pdf', base64_decode($row['pdfs']));		
		}
		$zip->close();
	}
	if(filesize($file) != 0){
		// Stream the file to the client 
		header("Content-Type: application/zip"); 
		header("Content-Length: " . filesize($file)); 
		header("Content-Disposition: attachment; filename=\"All_Prop_PDF.zip\""); 
		readfile($file); 
		unlink($file); 
	}
}
else{
	//
	$query="SELECT * FROM BATCH_PROP WHERE prop='".$propid."'";
	$result=doSqlQuery($query);
	
	if (!$result) die('Couldn\'t find '.$propid);
	
	$num_fields = mysqli_num_fields($result);
	$num=mysqli_num_rows($result);
	
	if($num != 1)
		die("Too many results");
	
	if($row = mysqli_fetch_array($result)){
		$pdfs = $row['pdfs'];
		$finalpdf = base64_decode($pdfs);
	}
	
	if($finalpdf != null){
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename='.$propid.'.pdf');
		header('Pragma: no-cache');
		header('Expires: 0');
		echo $finalpdf;
	}
}
echo "No Results Found";
die;
?>