<?php
session_start();
include 'library/prop_class.php';
include 'library/functions.php';
include 'library/presentation.php';
$debug = false;
$COMPSTODISPLAY = 100;
$LIMIT=NULL;
$TRIMINDICATED = false;
$INCLUDEMLS = false;
$MULTIHOOD = false;

global $INDICATEDVAL;

//Treat like equity so we use market val and not sales price
$isEquityComp = true;

//Parse Inputs
for($i=0; $i < $c; $i++)
{
	if($keys[$i] == 'propid'){
		$targ = $_GET['propid'];
		$targ = trim($targ);
		$propid = $targ;
		$targ = NULL;
	}
	if($keys[$i] == 'display'){
		$targ = $_GET['display'];
		$targ = trim($targ);
		$COMPSTODISPLAY = intval($targ);
		$targ = NULL;
	}
	if($keys[$i] == 'style'){
		$targ = $_GET['style'];
		$targ = trim($targ);
		if($targ == 'sales')
			$isEquityComp = false;
		if($targ == 'on')
			
		$targ = NULL;
	}
	if($keys[$i] == "trimindicated"){
		$targ = $_GET['trimindicated'];
		$targ = trim($targ);
		if($targ == "on")
			$TRIMINDICATED = true;
		$targ = NULL;
	}
	if($keys[$i] == "includemls"){
		$targ = $_GET['includemls'];
		$targ = trim($targ);
		if($targ == "on")
			$INCLUDEMLS = true;	
		$targ = NULL;
	}
	if($keys[$i] == "limit"){
		$targ = $_GET['limit'];
		$targ = trim($targ);
		if($targ > 0)
			$COMPSTODISPLAY = $targ;
		$targ = NULL;
	}
	if($keys[$i] == "multihood"){
		$targ = $_GET['multihood'];
		$targ = trim($targ);
		if($targ == "on")
			$MULTIHOOD = true;
		$targ = NULL;
	}
}

if($propid != "")	$abort = false;

if($abort){
	echo "<p>Please enter a value</p>";
	exit;
}


$property = getProperty($propid);


if($INCLUDEMLS)
	$compsarray = findBestComps($property,$isEquityComp,$TRIMINDICATED,null,$MULTIHOOD);
else //Just use Sales table
	$compsarray = findBestComps($property,$isEquityComp,$TRIMINDICATED, $TABLE_SALES,$MULTIHOOD);


if(sizeof($compsarray) == 0)
	return returnNoHits($propid);
	
usort($compsarray,"cmpProp");
		
$comp_min = MIN($COMPSTODISPLAY,count($compsarray));
$subjcomparray = array();
$subjcomparray[0] = $property;

for($i=0; $i < $comp_min; $i++)
{
	$subjcomparray[$i+1] = $compsarray[$i];
}
$_SESSION[$MEANVAL[0]] = getMeanVal($subjcomparray);
$_SESSION[$MEANVALSQFT[0]] = getMeanValSqft($subjcomparray);
$_SESSION[$MEDIANVAL[0]] = getMedianVal($subjcomparray);
$_SESSION[$MEDIANVALSQFT[0]] = getMedianValSqft($subjcomparray);

//putPropHistory($propid,getMeanVal($subjcomparray),$property->getFieldByName($INDICATEDVAL[0]),$property->mNeighborhood);

createGenericTable($subjcomparray,$isEquityComp);
?>