<?php
echo "\n--------------------------------------------------------------\n";
echo "Current Working dir: " . getcwd() . "\n";

set_include_path(get_include_path() . PATH_SEPARATOR . getcwd());

//Uncomment to debug
ini_set('error_reporting', E_ALL);
ini_set('display_errors', "stderr");

chdir(getcwd());

include('BatchPDF.php');

BatchPDF::run();

?>