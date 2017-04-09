<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "/Users/nick/github/five-stone-property-tax_PhpStorm");

echo "\n--------------------------------------------------------------\n";
echo "Current Working dir: " . getcwd() . "\n";

//Uncomment to debug
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', "stderr");

chdir('/Users/nick/github/five-stone-property-tax_PhpStorm');

include('BatchPDF.php');

BatchPDF::run();

?>