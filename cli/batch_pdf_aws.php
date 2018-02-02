<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "/var/www/html/fivestone");

echo "\n--------------------------------------------------------------\n";
echo "Current Working dir: " . getcwd() . "\n";
chdir('/var/www/html/fivestone');

include('BatchPDF.php');

BatchPDF::run();
?>