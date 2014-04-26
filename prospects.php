<?php
include_once 'library/prospectFunc.php';
include_once 'library/presentation.php';

$MAX_SALESPERHOOD=5;
$MIN_SALESPERHOOD=2;
$GENERATE=false;
$LOOKUP=false;
$SINGLETON=false;

//Parse Inputs
for($i=0; $i < $c; $i++)
{
	if($keys[$i] == 'prospects'){
		$targ = $_GET['prospects'];
		$targ = trim($targ);
		if($targ=='Generate')
			$GENERATE=true;
		if($targ=='Lookup')
			$LOOKUP=true;
		if($targ=='Singleton')
			$SINGLETON=true;
		$targ = NULL;
	}
	if($keys[$i] == 'propid'){
		$targ = $_GET['propid'];
		$targ = trim($targ);
		$singletonProp = $targ;
		$targ = NULL;
	}
}

emitHTMLHeader2();

if($GENERATE){
	echo 'Found ' . getTotalHoodCount() . ' distinct neighborhoods<br>';
	getProspects();
}

if($LOOKUP){
	$lStats = getLookupStats();
	$start = 0;
	echo 'Of '. $lStats['total']. ', '.$lStats['remaining'].' remain to be looked up<br>';	
	flush();
	//TODO change to exec check for existing php-cli commands
	while($lStats['remaining']>0){
		if($production==false)
			$phpCmd = "/Applications/MAMP/bin/php/php5.2.17/bin/php ";
		else
			$phpCmd = "php-cli ";
		$filename = "./cli/prospectLookups.php";
		$times = 10000;
		$wait = 5;
		for($i=0;$i<3;$i++){
			$myWait = $wait * $i;
			$output = shell_exec("$phpCmd $filename $times $start $myWait > /dev/null 2> error_log &");
			$start = $start+$times;
		}
		if($lStats['remaining'] < $start)
			break;
		$lStats2 = getLookupStats();
		flush();
		while($lStats2['remaining'] > $lStats['remaining'] - (.65 * $start)){
			sleep($wait*10);
			$lStats2 = getLookupStats();
			echo 'Remaining: '. $lStats2['remaining'].'<br>';
			if($debug) {
				echo 'ls2 '. $lStats2['remaining']. ', ls'.$lStats['remaining'].', ls-start,' . $start .'<br>';
			}
			flush();
		}
		$lStats = getLookupStats();
		echo 'Of '. $lStats['total']. ', '.$lStats['remaining'].' remain to be looked up<br>';
	}
}

if($SINGLETON){
	$debug=true;
	echo "Processing Singleton: ".$singletonProp."<br>";
	$singResult = processSingleton($singletonProp,false,4,true);
	if($debug) {
		if($singResult['comps'] == "")
			echo "No sales comps found for ". $singletonProp."<br>";
		else
			echo $row['prop_id'] . " singResult>>".$singResult['market_val']." | ".$singResult['mean_val']." | ".$singResult['comps']."<br>";
	}
	if($singResult['comps'] == ""){
		if(moveToNone($singletonProp))
			echo "Moved to None<br>";
	}else{
		if(updateProspect($singletonProp,$singResult))
			echo "Updated<br>";
	}
}

emitHTMLFooter();

?>