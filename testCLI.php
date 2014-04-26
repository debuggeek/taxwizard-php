<?php

$filename = "./cli/prospectLookups.php";
$times = 1;
$wait = 1;
$start = 0;
//for($i=0;$i<5;$i++){
	$myWait = $wait * $i;
	$output = shell_exec("php-cli $filename $times $start $myWait > /dev/null 2>&1 &");
	$start = $start+$times;
//}

?>
