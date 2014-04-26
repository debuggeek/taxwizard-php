<?php
include 'batch.php'; 

$debug = true;

if($debug)
	echo(var_dump($argv));

$IN = "./inputFolder/";
$OUT = "./completedBatch";

$propStr = $argv[1];

processSingleton($propStr);

$infile = $IN . $propStr;
$outfile = $OUT . $propStr;

if (copy($infile,$outfile)) {
	if(!unlink($outfile))
		error_log("Unable to delete file:" . $outfile );
	else{
		if($debug) echo("file copy successful");
	}
} else 
	error_log("Unable to copy file :". $infile." to ".$outfile);

?>