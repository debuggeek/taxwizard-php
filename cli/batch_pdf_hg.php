<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "/home1/cykoduck/public_html/debuggeek.com/fivestone/");

echo "\n--------------------------------------------------------------\n";
echo "Current Working dir: " . getcwd() . "\n";
chdir('/home1/cykoduck/public_html/debuggeek.com/fivestone/');

include('BatchPDF.php');

BatchPDF::run();

?>