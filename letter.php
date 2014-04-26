<?php
session_start();
include 'library/prop_class.php';
include 'library/functions.php';
include 'library/presentation.php';

$date = "December 4, 2009";
$owner = "Mr. John Krueger";
$owneraddr = "12920 Little Dipper Path";
$ownercityzip = "Austin, TX 78732";
//$propid = "710360";
$propaddr = "12916 Little Dipper Path";
$hoodPriceSqft = "124";
$propPriceSqft = "142";
$propSavings = "1,676";
$abort = true;

$hood = "";
$agents = false;

$fieldsofinterest = array($PROPID,$GEOID,$SITUS,$NEIGHB,$NEIGHBMIA,NULL,$MARKETVALUE,$MARKETPRICESQFT,$AGENT);
$_SESSION['target'] = new propertyClass;
$debug = false;

if($debug) set_time_limit(0);

//Parse Inputs
for($i=0; $i < $c; $i++)
{
	//echo "$keys[$i]";
	if($keys[$i] == 't'){
		$targ = $_GET['t'];
		$targ = trim($targ);
		$_SESSION['target']->mPropID = $targ;
		$targ = NULL;
	}
	
	if($keys[$i] == 'hood'){
		$targ = $_GET['hood'];
		$targ = trim($targ);
		$hood = $targ;
		$targ = NULL;
	}
	
	if($keys[$i] == 'agents'){
		$targ = $_GET['agents'];
		$targ = trim($targ);
		if($targ == "on")
			$agents = true;
	}

}

if($_SESSION['target']->mPropID != "")	$abort = false;
if($hood != "") $abort = false;

if($abort){
	echo "<p>Please enter a value</p>";
	exit;
}

$propid = $_SESSION['target']->mPropID;

$props = getHoodList($hood,true,NULL);
$avg = getMktSqftAvg($props);

emitHTMLHeader();
echo "<p>".count($props)." found with an average price of ".number_format($avg,2)."</p>".PHP_EOL;

if($debug) iterateProps($props);

//target props are properties whose avg is higher then the hood average
$targetprops = getAboveAvgProps($props,$avg);

echo "<p>Found ".count($targetprops)." properites higher then the average</p>".PHP_EOL;

iterateProps($targetprops);

emitHTMLFooter();
//lookupPropID($propid,$fieldsofinterest);
//generateLetter();

function savings($property){
	global $avg;
	return $property->mMarketVal - ($property->mLivingArea * $avg);
}

function getAboveAvgProps($proparray,$avgval){
	$resultarray = array();
	foreach($proparray as $key => $prop){
		$avg = $prop->getMrktSqft();
		if((float)$avg > (float) $avgval)
			$resultarray[] = $prop;
	}
	return $resultarray;
}

function getMktSqftAvg($proparray){
	$result = 0;
	$count = 0;
	$skipped = 0;
	$i=0;
	
	foreach($proparray as $prop){
		$val = $prop->getMrktSqft();
		if($val > 0){
			$result = $result + $val;
			$count++;
		}
		else{
			echo "<br>Error on propid:".$prop->mPropID."<br>".PHP_EOL;
		}
	}
	if($count != count($proparray)){
		echo "final count(".$count.") doesn't match proparray count:". count($proparray);
	}
	return ($result / $count);
}


/**
function sqldbconnect(){
	global $username,$password,$database;

	mysql_connect('localhost',$username,$password);
	@mysql_select_db($database) or die( "Unable to select database");

}

/**
 * @param string $hood
 * @return array of property ids
 */

/*
function getLivingArea($propid)
{
	global $LIVINGAREA;

	sqldbconnect();
	$query="SELECT * FROM ". $LIVINGAREA["TABLE"] . " WHERE prop_id='$propid'";

	//	echo $query;

	$result=mysql_query($query);
	$num=mysql_numrows($result);
	mysql_close();

	if(!$result)
	return "No Value Found!";
	elseif($num > 1)
	return "UNEXPECTED ERROR:More then 1 result found";

	$row = mysql_fetch_array($result);
	return $row[$LIVINGAREA[2]];

}


function emitHTMLHeader(){
	echo '<HTML>'.PHP_EOL;
	echo '<HEAD><link rel="stylesheet" type="text/css" href="default.css" /> '.PHP_EOL;
	echo '</HEAD>'.PHP_EOL;
	echo '<BODY>'.PHP_EOL;
}

function emitXML(propertyClass $propClasses){
	echo nl2br('<PROPERTIES>');
	
	for($i=0; $i < $propClasses.count(); $i++){
		if($i = 0)
			echo nl2br('<PROPTYPE>subject</PROPTYPE>');
		else
			echo nl2br('<PROPTYPE>');
	}
	
	echo nl2br('</PROPERTIES>');
}

function emitHTMLFooter(){
	echo '</BODY>'.PHP_EOL;
	echo '<HTML>';
}*/

?>