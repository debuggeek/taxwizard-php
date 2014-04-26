<?php
include_once("prospectFunc.php");
$debug = false;

if($debug) echo(var_dump($argv));

if(count($argv)>5 || count($argv)<2){
	echo "Invalid input\n";
	echo "Usage: prospectLookups <number_to_process> [<start> [<wait>]]";
	die();
}

if(count($argv) >= 2){
	$numToProcess = $argv[1];
}
if(count($argv) >= 3){
	$start = $argv[2];
}
if(count($argv) == 4){
	$wait = $argv[3];
	sleep($wait);
}

$lookupCount = processLookups($numToProcess,$start);
echo("Processed " . $lookupCount . " entries");
?>