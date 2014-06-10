<?php
include 'library/propertyClass.php';
session_start();

$subjarray = array();
$export = false;

include 'library/defines.php';
include 'library/functions.php';
include 'library/presentation.php';


$_SESSION['numcomps'] = 0;
$_SESSION['subjsess'] = new propertyClass();

//MAIN
$isEquityComp = true;
//Parse Inputs
{
	error_reporting(E_ALL & ~E_NOTICE);
	parse_str($_SERVER['QUERY_STRING'], $query_array);
	$subjuri = $query_array['s'];
	$subjuri = trim($subjuri);
	$_SESSION['subjsess']->mPropID = $subjuri;
	$subjuri == NULL;
	for($i=0;$i < sizeof($query_array); $i++)
	{
		if($query_array["c".$i] != null)
		{
			$comparr[$i] = $query_array["c".$i];
			$comparr[$i] = trim($comparr[$i]);
			$_SESSION['comp'.$i] = new propertyClass($comparr[$i]);
			$_SESSION['numcomps']=$_SESSION['numcomps'] + 1;
			if($query_array["c".$i."sp"])
				$_SESSION['comp'.$i]->SetField($SALEPRICE[0],$query_array["c".$i."sp"]);
			if($query_array["c".$i."sd"])
				$_SESSION['comp'.$i]->SetField($SALEDATE[0],$query_array["c".$i."sd"]);
			$comparr[$i] = NULL;
		}
	}
	if($query_array['Submit'] == 'Build Sales Table')
		$isEquityComp = false;
	else if($query_array['Submit'] == 'export')
		$export = true;
}

if($debug)
{
	echo "Subject is ". $_SESSION['subjsess']->mPropID."<br>";
	echo "Number of comps are ".$_SESSION['numcomps']."<br>";
	for($i=1;$i <= $_SESSION['numcomps'];$i++){
		echo "Comp ".$i." is ".$_SESSION['comp'.$i]->mPropID."<br>";
	}
}



if($_SESSION['subjsess']->mPropID == "")
{
	echo "<p>You must choose a subject property.  Click Back</p>";
	exit;
}

// check for a search parameter
if (!isset($_SESSION['subjsess']->mPropID))
{
	echo "<p>We dont seem to have a subject parameter!</p>";
	exit;
}
$subj = $_SESSION['subjsess']->mPropID;

lookupProperty($subj);

for($i=1;$i <= $_SESSION['numcomps'];$i++)
{
	$comp = $_SESSION['comp'.$i]->mPropID;
	lookupProperty($comp);
}

if ($debug) echo $query;

$_SESSION['subjsess']->$MEANVAL[2]($_SESSION['numcomps']);
$_SESSION['subjsess']->$MEDIANVAL[2]($_SESSION['numcomps']);

if($debug)
{
echo "<br/>subj: " . var_dump($_SESSION['subjsess']) . "<br/>";
for($i=1;$i <= $_SESSION['numcomps'];$i++)
echo "<br/>data ".$i.": " . var_dump($_SESSION['comp'.$i]) . "<br/>";
}

//emitHTMLHeader();
#@todo where I left off
$compsarray = array();
$currcomp = 1;
for($i=0;$i <= $_SESSION['numcomps'];$i++)
{
	$compsarray[$i] = $currcomp;
	$currcomp++;
}

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

createGenericTable($subjcomparray,$isEquityComp);
/*
if($export)
	{
	//CSV expects full array with subj at front
	array_unshift($compsarray,$_SESSION['subjsess']);
	createEQTableCSV($compsarray);
	}
else if($isEquityComp){
	createEQTable($compsarray);
}
else{
	createSalesTable($compsarray);
}*/

//emitHTMLFooter();
?>