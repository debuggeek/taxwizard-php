<?php
session_start();
include 'library/propertyClass.php';
include 'library/functions.php';
include 'library/presentation.php';
$debug = false;
$COMPSTODISPLAY = 50;
$LIMIT=NULL;

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
	if($keys[$i] =='limit'){
		$targ = $_GET['limit'];
		$targ = trim($targ);
		$LIMIT = $targ;
		$targ = NULL;
	}
	if($keys[$i] == 'display'){
		$targ = $_GET['display'];
		$targ = trim($targ);
		$COMPSTODISPLAY = intval($targ);
		$targ = NULL;
	}
	if($keys[$i] == 'equity'){
		$targ = $_GET['equity'];
		$targ = trim($targ);
		if($targ == 'false')
			$isEquityComp = false;
		$targ = NULL;
	}
}

if($propid != "")	$abort = false;

if($abort){
	echo "<p>Please enter a value</p>";
	exit;
}

$property = getProperty($propid);

$compsarray = findBestComps($property,$isEquityComp,.75);

$subjsqft = $property->getFieldByName($LIVINGAREA["NAME"]);
$min = .8 * $subjsqft;
$max = 1.2 * $subjsqft;
	
emitHTMLHeader2();

echo "<BR>".count($compsarray)." Results found in the same Neighborhood between ".$min." and ".$max." SQFT<BR>".PHP_EOL;
echo "<BR>Displaying Best ".$COMPSTODISPLAY."<BR>".PHP_EOL;
usort($compsarray, "compareIndicatedVal");
		
$comp_min = MIN($COMPSTODISPLAY,count($compsarray));

$proparray = array();
$proparray[0] = $property;

for($i=0; $i < $comp_min; $i++)
{
	$proparray[$i+1] = $compsarray[$i];
}

$mean = getMeanVal($proparray);
$meansqft = getMeanValSqft($proparray);
$median = getMedianVal($proparray);
$mediansqft = getMedianValSqft($proparray);

echo '<H2>Comp Discovery - '.date('l jS \of F Y h:i:s A').'</H2>'.PHP_EOL;
echo '<H1> Mean: '.$mean.'   Mean/SQFT:'.$meansqft.'     Median:'.$median.'   Median/SQFT:' .$mediansqft;
dumpProperties($proparray,$isEquityComp);
//createeqtable($proparray);
emitHTMLFooter();
?>