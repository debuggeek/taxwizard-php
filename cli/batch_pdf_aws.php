<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "/var/www/html");

echo "\n--------------------------------------------------------------\n";
echo "Current Working dir: " . getcwd() . "\n";
chdir('/var/www/html');

include('BatchPDF.php');

BatchPDF::run();
?>