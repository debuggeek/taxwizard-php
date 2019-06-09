<?php
declare(strict_types=1);

$debug = true;
$debugquery = false;
include_once 'defines.php';
include_once 'propertyClass.php';
include_once 'queryContext.php';
include_once 'PropertyDAO.php';

use TaxWizard\TcadScore;
require_once 'TcadScore.php';

/**
 * @param propertyClass[] $subjcomp
 * @return int
 */
function getMeanVal($subjcomp) : int
{
	global $INDICATEDVAL;
	$result = 0;
	$compCount = count($subjcomp) -1;
	//don't include subj
	for($i=1;$i <= $compCount; $i++){
		$next = $subjcomp[$i]->getIndicatedVal(false);
		$result += $next;
	}
	if($compCount > 0){
	    $result = intval(round($result / $compCount));
    } else {
        error_log("getMeanVal: No comps to average over");
        $result = 0;
    }
	return $result;
}

/**
 * @param propertyClass[] $subjcomp
 * @return float
 */
function getMeanValSqft($subjcomp) : float {
	global $INDICATEDVALSQFT;
	$result = 0;
	if(count($subjcomp) > 1){
		$comps = count($subjcomp) -1;
		for($i=1;$i <= $comps; $i++) {
            $result += $subjcomp[$i]->getIndicatedValSqft();
        }
		$result = $result / $comps;
	}
	return $result;
}

/**
 * @param propertyClass[] $subjcomp
 * @return int
 */
function getMedianVal($subjcomp): int {
	global $INDICATEDVAL;

	$median = 0;

	if(count($subjcomp) > 1) {
		$comparray = array();

		for ($i = 1; $i < count($subjcomp); $i++) {
            $next = $subjcomp[$i]->getIndicatedVal(false);
			$comparray[] = $next;
		}

		$num = count($comparray);
		sort($comparray);

		if ($num % 2) {
			$median = $comparray[floor($num / 2)];
		} else {
			$median = ($comparray[$num / 2] + $comparray[$num / 2 - 1]) / 2;
		}
	}
	return intval(round($median));
}

/**
 * @param propertyClass[] $subjcomp
 * @return float
 */

function getMedianValSqft($subjcomp) : float {
	global $INDICATEDVALSQFT;

	$median = 0;
	if(count($subjcomp) > 1) {
		$comparray = array();

		for ($i = 1; $i < count($subjcomp); $i++)
			$comparray[] = $subjcomp[$i]->getIndicatedValSqft();

		$num = count($comparray);
		sort($comparray);

		if ($num % 2) {
			$median = $comparray[floor($num / 2)];
		} else {
			$median = ($comparray[$num / 2] + $comparray[$num / 2 - 1]) / 2;
		}
	}
	return $median;
}

/**
 * @return mysqli
 */
function sqldbconnect()
{
	global $servername,$username,$password,$database,$dbport;

	// Create connection
    $mysqli = new mysqli($servername, $username, $password, $database, intval($dbport));

	
	// Check connection
    if (mysqli_connect_errno()) {
        die("Connection failed: " . mysqli_connect_error());
    } 
    
//	@mysqli_select_db($database) or die( "Unable to select database");
	return $mysqli;
}

/**
 * @return queryResult
 */

function doSqlQuery($query){
	global $debugquery;

    $mysqli = sqldbconnect();
	if($debugquery) error_log("query:".$query);
	$result=$mysqli->query($query);
    $mysqli->close();
    if($debugquery){
        if (!$result){
            error_log("false query came back:".$result);
        } else {
            error_log("query came back:".var_dump($result));
        }
    }
	return $result;
}

function hasDelta($class){
	global $landvaladjdelta,$classadjdelta,$goodadjdelta,$lasizeadjdelta,$mktlevelerdetailadjdelta,$segmentsadjdelta;
	global $LANDVALUEADJ,$CLASSADJ,$GOODADJ,$LASIZEADJ,$MKTLEVELERDETAILADJ,$SEGMENTSADJ,$SEGMENTSADJSIMPLE;
	
	if($class === NULL)
		return NULL;
	
	switch($class)
	{
		case($LANDVALUEADJ["NAME"]):
			return $landvaladjdelta;
		case($CLASSADJ["NAME"]):
			return $classadjdelta;
		case($GOODADJ["NAME"]):
			return $goodadjdelta;
		case($LASIZEADJ["NAME"]):
			return $lasizeadjdelta;
		case($MKTLEVELERDETAILADJ["NAME"]):
			return $mktlevelerdetailadjdelta;
		case($SEGMENTSADJ["NAME"]):
        case($SEGMENTSADJSIMPLE["NAME"]):
			return $segmentsadjdelta;
		default:
			return false;
	}
}

function hasMultiRow($class){
	global $SEGMENTSADJ, $segmentsadjMultiRow;

	if($class === NULL)
		return NULL;

	switch($class)
	{
		case($SEGMENTSADJ["NAME"]):
			return $segmentsadjMultiRow;
		default:
			return NULL;
	}
}

/**
 * @param propertyClass $subj
 * @param propertyClass $currprop
 * @param bool $isEquity
 * @throws Exception
 */
function calcDeltas($subj,$currprop, $isEquity)
{
    $currprop->setLandValAdjDelta($subj);
    $currprop->setClassAdjDelta($subj);
    $currprop->setGoodAdjDelta($subj, $isEquity);
    $currprop->setLASizeAdjDelta($subj);
    $currprop->setMktLevelerDetailAdjDelta($subj);
    $currprop->setSegAdjDelta($subj);

    $currprop->setImpDets(ImpHelper::compareImpDetails_AddDelta($subj->getImpDets(), $currprop->getImpDets()));
    $tcadScore = new \TaxWizard\TcadScore();
    $tcadScore->setScore($subj, $currprop);
    $currprop->setTcadScore($tcadScore);

    $currprop->setNetAdj($currprop->calcNetAdj());
    $currprop->setIndicatedVal($currprop->calcIndicatedVal($isEquity));
    $currprop->getIndicatedValSqft();
}

function getSubjProperty($propid){
    $property = getProperty($propid);
    $property->setisSubj(true);
    $property->setIndicatedVal(null); // Changed per 2019 request from $property->getMarketVal());
    return $property;
}

/**
 * @param string $propid
 * @return propertyClass when successful
 *
 * Note: this function will NOT give delta information just basic and calculated data of property
 * @throws Exception
 */
function getProperty($propid)
{
    $debug=false;

    global $servername,$username,$password,$database, $dbport;
    $propDao = new PropertyDAO($servername, $username, $password, $database);
    try {
        $currprop = $propDao->getPropertyById($propid);
    } catch(Exception $e){
        throw new Exception("Unable to get property due to : " . $e->getMessage());
    }
    $currprop->setisSubj(false);
    return $currprop;
}


/**
 * Retrieves an array of prop_class objects based on the neighborhood code
 * @param String $hood
 * @param queryContext $queryContext
 * @return propertyClass[]
 * @throws Exception
 */
function getHoodList($hood, queryContext $queryContext){
	global $NEIGHB, $debugquery;

	$year = date("Y");
	$hoodSearch = $NEIGHB["HOOD"] ."='$hood'";
	
	if($queryContext->multiHood){
		//Change the hood from something like K1005 to K10**
		$subHood = substr($hood,0,-2);
		$hoodSearch = $NEIGHB["HOOD"] ." LIKE '$subHood%'";
	}

	global $servername,$username,$password,$database, $dbport;
	$propDao = new PropertyDAO($servername, $username, $password, $database);
	return $propDao->getHoodProperties($hood, $queryContext);
}

/**
 * Takes a subject property and returns the comparables from the same neighborhood
 * where the square footage of the compared properties are within 25% of the subject (as of 2014 rules).
 * This function will also remove any properties of class 'XX'
 * @param propertyClass $subjprop
 * @param queryContext queryContext
 * @return propertyClass[]
 * @throws Exception
 */
function findBestComps(propertyClass $subjprop, queryContext $queryContext)
{
	global $NEIGHB,$LIVINGAREA,$PROPID,$debug,$isEquityComp;
    $compsarray = array();
    /** @var responseContext $responseCtx */
    $responseCtx = $queryContext->responseCtx;

//    $debug=true;

	//set global correctly, cuz prop_class uses this
	$isEquityComp = $queryContext->isEquityComp;

    if($debug) error_log("DEBUG\tfindBestComps Start Memory >> ". memory_get_usage() . "\n");
    if($debug) error_log("DEBUG\tfindBestComps subj: " . var_dump($subjprop));

    if($debug) error_log($subjprop->getFieldByName($LIVINGAREA["NAME"]));

    //Now that we have all comps we only want ones where the sqft / LA is within 25%
    $subjsqft = $subjprop->getFieldByName($LIVINGAREA["NAME"]);
    if($subjsqft == 0){
        error_log("findBestComps: subject sqft == 0 exiting");
        return $compsarray;
    }

	$comps = getHoodList($subjprop->getFieldByName($NEIGHB["NAME"]),$queryContext);
    if($queryContext->traceComps) error_log("TRACE\tfindBestComps: found ".count($comps). " possible comps in hood list");
    $responseCtx->unfilteredPropCount = count($comps);
    //Track for duplicates
    $compsSeen = array();

	foreach($comps as $comp)
	{
        if(addToCompsArray($comp,$subjprop,$queryContext)){
            if($queryContext->traceComps) error_log("TRACE\tfindBestComps: Adding ".$comp->getPropID(). " as comp::".$comp);
            $compsarray[] = $comp;
        } else {
            if($queryContext->traceComps) error_log("TRACE\tfindBestComps: Skipped adding ".$comp->getPropID()." as comp to ".$subjprop->getPropID());
        }
        $compsSeen[] = $comp->getPropID();
	}
	error_log("findBestComps: compsarray count= ".count($compsarray). " sizeof=".sizeof($compsarray));
	return $compsarray;
}

/**
 * Determines if the passed in comp should be compared against the subj property
 * @param propertyClass $c
 * @param propertyClass $subjprop
 * @param queryContext $queryContext
 * @return bool
 */
function addToCompsArray(propertyClass $c,propertyClass $subjprop, queryContext $queryContext)
{
    global $LASIZEADJ;
    $min = $max = 0;
    $compsseen = array();

    if ($c->getPropID() == $subjprop->getPropID()) {
        error_log("INFO\taddToCompsArray: Skipping Comp prop id matched subject:" . $c->getPropID());
        return false;
    }

    //For a single improvement property the LASize and Sqft should be the same, but for multiImp we need just the primary
    $subjsqft = $subjprop->getLASizeAdj();
	//sqftPercent is stored as int so convert to percentage
    if($queryContext->sqftPercent != null) {
        $percentAllowed = $queryContext->sqftPercent * .01;
        $min = $subjsqft - ($percentAllowed * $subjsqft);
        $max = (1 + $percentAllowed) * $subjsqft;
    } else {
        $min = $queryContext->sqftRangeMin;
        $max = $queryContext->sqftRangeMax;
    }

    if($c->getImpCount() == 0){
        $msg = sprintf("%u skipped due to 0 detail improvements found", $c->getPropID());
        if ($queryContext->traceComps) error_log("TRACE\taddToCompsArray: ".$msg);
        //Might be error that we have no improvements
        $queryContext->responseCtx->errors[] = $msg;
        return false;
    }

    $sqft = $c->getFieldByName($LASIZEADJ["NAME"]);

    if ($sqft < $min || $sqft > $max) {
        $msg = sprintf("%u removed as potential comp due to size=%u min=%u max=%u subj size=%u", $c->getPropID(), $sqft, $min, $max, $subjsqft);
        if ($queryContext->traceComps) error_log("TRACE\taddToCompsArray: ".$msg);
        $queryContext->responseCtx->infos[] = $msg;
        return false;
    }

    //Check sale type.
    //2014 : Can't include VU
    //2018 : Use list check
    if(!$queryContext->isEquityComp && !isDesiredSaleType($c, $queryContext->salesTypes)){
        $msg = sprintf("%u removed as potential comp due to saleType=%s",$c->getPropID(), $c->getSaleType());
        if ($queryContext->traceComps) error_log("TRACE\taddToCompsArray: ".$msg);
        $queryContext->responseCtx->infos[] = $msg;
        return false;
    }

    // Check class
    //2013 : Can't include XX

    $badClass = "XX"; //don't include this type as it's not a good property to use
    if ($c->getClassCode() === null) {
        $msg = sprintf("%u has no class data and will be skipped", $c->getPropID());
        error_log("ERROR> addToCompsArray: " . $msg);
        $queryContext->responseCtx->errors[] = $msg;
        return false;
    }
    //Only review further if badClass string not found
    if ($c->getClassCode() === $badClass) {
        $msg = sprintf("%u has bad class %s data and will be skipped", $c->getPropID(), $badClass);
        if ($queryContext->traceComps) error_log("addToCompsArray: ".$msg);
        $queryContext->responseCtx->errors[] = $msg;
        return false;
    }

    if ($queryContext->subClassRangeEnabled) {
        if (!fallsInsideClassRange($subjprop->getSubClass(), $c->getSubClass(), $queryContext->subClassRange)) {
            $msg = sprintf("%u failed to fall inside class range and will be skipped", $c->getPropID());
            if ($queryContext->traceComps) error_log("addToCompsArray: ");
            $queryContext->responseCtx->errors[] = $msg;
            return false;
        }
    }

    //Check Percent Good
    if ($queryContext->percentGoodRangeEnabled) {
        if (!fallsWithinPercentGood($c, $subjprop, $queryContext)) {
            $msg = sprintf("%u failed to fall inside percent good range and will be skipped", $c->getPropID());
            if ($queryContext->traceComps) error_log("addToCompsArray: ".$msg);
            $queryContext->responseCtx->errors[] = $msg;
            return false;
        }
    }

    // Check limit filter
	if ($queryContext->limitToLessImps){
		$varCompImpCount = count(ImpHelper::getUniqueImpIds($c->getImpDets()));
		$varSubjImpCount = count(ImpHelper::getUniqueImpIds($subjprop->getImpDets()));
		if($varCompImpCount > $varSubjImpCount){
            $msg = sprintf("%u failed due to more subjects them prop and will be skipped", $c->getPropID());
            if ($queryContext->traceComps) error_log("addToCompsArray: ".$msg);
            $queryContext->responseCtx->errors[] = $msg;
			return false;
		}
	}

	// Check Sale Ratio
    if ($queryContext->saleRatioEnabled){
	    if(!fallsWithinSaleRatio($c, $queryContext)){
            $msg = sprintf("%u failed to fall inside sale ratio range and will be skipped", $c->getPropID());
            error_log("INFO\taddToCompsArray: ".$msg);
            $queryContext->responseCtx->errors[] = $msg;
            return false;
        }
    }

    if($queryContext->traceComps) error_log("TRACE\tTesting deltas for ". $c->getPropID());
    try{
        calcDeltas($subjprop,$c, $queryContext->isEquityComp);
    } catch (Exception $e){
        $error = sprintf("%u failed to calcDelta and will be skipped due to: %s", $c->getPropID(), $e->getMessage());
        error_log("ERROR\taddToCompsArray: ".$error);
        $queryContext->responseCtx->errors[] = $error;
        return false;
    }

    if ($queryContext->netAdjustEnabled){
        error_log("INFO\taddToCompsArray: Filtering for net adjustment amount of " . $queryContext->netAdjustAmount);
        if(!fallsWithinNetAdjRange($c, $queryContext->netAdjustAmount)){
            $msg = sprintf("%u failed to fall inside net adjustment range and will be skipped", $c->getPropID());
            error_log("INFO\taddToCompsArray: ".$msg);
            $queryContext->responseCtx->errors[] = $msg;
            return false;
        }
    }

    if($queryContext->limitTcadScores){
        $tcadScore = $c->getTcadScore();

        if($queryContext->limitTcadScoresAmount != null){
            $min = $queryContext->limitTcadScoresAmount;
            $max = 100;
        } else {
            $min = $queryContext->tcadScoreLimitMin;
            $max = $queryContext->tcadScoreLimitMax;
        }
        if($tcadScore->getScore() < $min){
            if($queryContext->traceComps){
                error_log("TRACE\taddToCompsArray: comp ".$c->getPropID(). " tcad score ". $tcadScore->getScore()
                    . " falls below threshold ". $min);
            }
            return false;
        }
        if($tcadScore->getScore() > $max){
            if($queryContext->traceComps){
                error_log("addToCompsArray: comp ".$c->getPropID(). " tcad score ". $tcadScore->getScore()
                    . " falls above threshold ". $max);
            }
            return false;
        }
    }

    if($queryContext->trimIndicated){
        if(compareIndicatedVal($subjprop,$c)==1){
            if($queryContext->traceComps) error_log("TRACE\taddToCompsArray: Found comp ".$c->getPropID());
            return true;
        }
    } else {
        if($queryContext->traceComps) error_log("TRACE\taddToCompsArray: Found comp ".$c->getPropID());
        return true;
    }

    return false;
}

/**
 * Compares two properties for their indicated value
 * Returns 0 if equal , -1 if prop1 is less the prop2, or 1 if prop1 > prop2
 *
 * @param propertyClass $prop1
 * @param propertyClass $prop2
 * @return int
 */
function compareIndicatedVal(propertyClass $prop1, propertyClass $prop2)
{
    global $debug;

    $prop1_Ind = $prop1->getIndicatedVal(false);
    if($prop1_Ind == 0)
        error_log("Error during comparison for indicated value of propid:".$prop1->getPropID());
    $prop2_Ind = $prop2->getIndicatedVal(false);
    if($prop2_Ind == 0)
        error_log("Error during comparison for indicated value of propid:".$prop2->getPropID());
    if($debug) echo "<br/>Comparing indicated values of".$prop1_Ind." and ".$prop2_Ind."<br/>";
    if ($prop1_Ind == $prop2_Ind) {
        return 0;
    }
    return ($prop1_Ind < $prop2_Ind) ? -1 : 1;
}

/**
 * @param propertyClass $prop1
 * @param propertyClass $prop2
 * @param bool $trace
 * @return int
 * @throws Exception
 */
function compareTcadVal(propertyClass $prop1, propertyClass $prop2){

    $prop1_tcadScore = $prop1->getTcadScore()->getScore();
    if(!is_numeric($prop1_tcadScore)){
        throw new Exception("TCAD Score is not a number");
    }
    $prop2_tcadScore = $prop2->getTcadScore()->getScore();
    if(!is_numeric($prop2_tcadScore)){
        throw new Exception("TCAD Score is not a number");
    }

//    if($trace){
//        $err = sprintf("Comparing %s tcad score of %s with %s tcad score of %s", $prop1->getPropID(), $prop1_tcadScore, $prop2->getPropID(), $prop2_tcadScore);
//        error_log($err);
//    }

    if($prop1_tcadScore === $prop2_tcadScore){
        return 0;
    }
    return ($prop1_tcadScore > $prop2_tcadScore) ? -1 : 1;
}

/**
 * @param string $subjClassAdj
 * @param string $compClassAdj
 * @param int $range
 * @return bool
 */
function fallsInsideClassRange($subjSubClass, $compSubClass, $range){
	global $debug;

	$subClassRanges = array('2-','2','2+','3-','3','3+','4-','4','4+','5-','5','5+','6-','6','6+','7-','7','7+','8-','8','8+');
	$subjPos = array_search($subjSubClass, $subClassRanges);
	if($subjPos === false){
		error_log("fallsInsideClassRange: Couldn't find ". $subjSubClass . " in " . $subClassRanges . " for subject  SubClassAdj NOT in range");
		return false;
	}
	$compPos = array_search($compSubClass,$subClassRanges);
	if($compPos === false){
		error_log("fallsInsideClassRange: Couldn't find ". $compSubClass . " in " . $subClassRanges . " for comp assuming SubClassAdj NOT in range");
		return false;
	}
	if($debug){
		$string = "fallsInsideClassRange: subjPos: ". $subjPos . " compPos: ". $compPos . " range: ". $range;
		print($string);
		error_log($string);
	}
	if($compPos > $subjPos + $range){
		return false;
	}
	if($compPos < $subjPos - $range){
		return false;
	}
	
	return true;
}

/**
 * @param propertyClass $comp
 * @param queryContext $queryContext
 * @return bool
 */
function fallsWithinSaleRatio(propertyClass $comp, queryContext $queryContext){
    $compSaleRatio = $comp->getSaleRatio();

    if($compSaleRatio < $queryContext->saleRatioMin){
        return false;
    } else if($compSaleRatio > $queryContext->saleRatioMax){
        return false;
    }
    return true;
}

function fallsWithinPercentGood(propertyClass $comp, propertyClass $subjprop, queryContext $queryContext){
    global $debug;
    $subjPercentGood = intval($subjprop->getGoodAdj());
    $compPercentGood = intval($comp->getGoodAdj());

    if($queryContext->percentGoodRange != null){
        $min = $subjPercentGood - $queryContext->percentGoodRange;
        $max = $subjPercentGood + $queryContext->percentGoodRange;
    } else {
        $min = $queryContext->percentGoodMin;
        $max = $queryContext->percentGoodMax;
    }
    if($debug) error_log("fallsWithinPercentGood: subj ".$subjPercentGood." comp ". $compPercentGood . " range min ". $min . " max " . $max);

    if($compPercentGood < $min){
        if($debug) error_log("fallsWithinPercentGood: Comp falls below range");
        return false;
    }

    if($compPercentGood > $max){
        if($debug) error_log("fallsWithinPercentGood: Comp falls above range");
        return false;
    }

    return true;
}

function fallsWithinNetAdjRange(propertyClass $comp, $range){
    global $debug;
    $compNetAdj = intval($comp->getNetAdj());

    if($debug) error_log("fallsWithinNetAdjRange: comp ". $compNetAdj . " range ". $range);

    if($compNetAdj < ($range * -1)){
        if($debug) error_log("fallsWithinNetAdjRange: Comp falls below range");
        return false;
    }

    if($compNetAdj > $range){
        if($debug) error_log("fallsWithinNetAdjRange: Comp falls above range");
        return false;
    }

    return true;
}

function putPropHistory($propid,$mean_val,$indicated_val,$comps_csv){
	global $TABLE_PROSPECT_LIST,$debug;
	
	$indValInt = (int)str_replace(array(' ', ','), '', $indicated_val);
	$meanValInt = (int)str_replace(array(' ', ','), '', $mean_val);
	
	$query="UPDATE " . $TABLE_PROSPECT_LIST . " 
			SET computed_date='" . date("Y-m-d H:i:s") . "',
					market_val=,'" . $indicated_val . "',
					mean_val=" . $meanValInt . ",
					comps_csv='" . $comps_csv . "'
					WHERE prop_id=".$propid;
	
	if($debug) print("putPropHistory>> query:" . $query);
	$sqlResult = doSqlQuery($query);
		
	if(!$sqlResult){
		error_log("putPropHistory>> query failed:" . $query);
	}
}

function isNotMLS(propertyClass $property){
    $strCmpResult = strcasecmp($property->getSaleSource(), "MLS");
    return $strCmpResult != 0;
}

function isFlaggableSaleType(propertyClass $propertyClass){
    if($propertyClass->mSaleType == "VQ")
        return true;
    else
        return false;
}

function isDesiredSaleType(propertyClass $propertyClass,array $typesWanted): bool
{
    //an empty array means we are not limiting
    if(sizeof($typesWanted) == 0){
        return true;
    }
    // nonempty array means we check
    if(in_array($propertyClass->mSaleType, $typesWanted)){
        return true;
    }
    return false;
}

/**
 * @param propertyClass $property
 * @param queryContext $queryContext
 * @return array|null
 * @throws Exception
 */
function generateArrayOfBestComps(propertyClass $property, queryContext $queryContext)
{
    $compsarray = findBestComps($property, $queryContext);

    if (sizeof($compsarray) == 0) {
        error_log("generateArrayOfBestComps>>  no comps found for " . $property->getPropID());
        return null;
    }

    if (!$queryContext->includeMls) {
        $compsarray = array_filter($compsarray, "isNotMLS");
    }

    if (sizeof($compsarray) == 0) {
        error_log("generateArrayOfBestComps>> no comps found after MLS Sort for " . $property->getPropID());
    }

    error_log("INFO\tgenerateArrayOfBestComps>> found " . sizeof($compsarray) .
        " comp(s) for " . $property->getPropID() .
        " during " . ($queryContext->isEquityComp ? " equity " : " sales ") . "search");

    switch ($queryContext->rank){
        case RankType::Indicated:
            usort($compsarray, "compareIndicatedVal");
            break;
        case RankType::TCAD:
            usort($compsarray, "compareTcadVal");
            break;
        default:
            throw new Exception("UNDEFINED RANK SORT");
    }

    $responseCtx = $queryContext->responseCtx;
    /** @var responseContext $responseCtx */
    $responseCtx->filteredPropCount = count($compsarray);

    // Trim down the number of comps displayed based on user desire
    $comp_min = MIN($queryContext->compsToDisplay, count($compsarray));
    $subjcomparray = array();
    $subjcomparray[0] = $property;

    for ($i = 0; $i < $comp_min; $i++) {
        $subjcomparray[$i + 1] = $compsarray[$i];

    }

    return $subjcomparray;
}
