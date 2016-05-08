<?php
$debug = true;
$debugquery = false;
include_once 'defines.php';
include_once 'propertyClass.php';
include_once 'queryContext.php';
include_once 'PropertyDAO.php';

use TaxWizard\TcadScore;
require_once 'TcadScore.php';

function getMeanVal($subjcomp)
{
	global $INDICATEDVAL;
	$result = 0;
	$compCount = count($subjcomp) -1;
	//don't include subj
	for($i=1;$i <= $compCount; $i++){
		$next = str_replace(",","",$subjcomp[$i]->getFieldByName($INDICATEDVAL[0]));
		$result += $next;
	}
	if($compCount > 0){
	    $result = $result / $compCount;
    } else {
        error_log("getMeanVal: No comps to average over");
        $result = 0;
    }
	return number_format($result);
}

/**
 * @param array $subjcomp
 * @return string
 */
function getMeanValSqft($subjcomp){
	global $INDICATEDVALSQFT;
	$result = 0;
	if(count($subjcomp) > 1){
		$comps = count($subjcomp) -1;
		for($i=1;$i <= $comps; $i++)
			$result += $subjcomp[$i]->getFieldByName($INDICATEDVALSQFT[0]);

		$result = $result / $comps;
	}
	return number_format($result,2);
}

/**
 * @param array $subjcomp
 * @return string
 */
function getMedianVal($subjcomp){
	global $INDICATEDVAL;

	$median = 0;

	if(count($subjcomp) > 1) {
		$comparray = array();

		for ($i = 1; $i < count($subjcomp); $i++) {
			$next = str_replace(",", "", $subjcomp[$i]->getFieldByName($INDICATEDVAL[0]));
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
	return number_format($median);
}

/**
 * @param array $subjcomp
 * @return string
 */

function getMedianValSqft($subjcomp){
	global $INDICATEDVALSQFT;

	$median = 0;
	if(count($subjcomp) > 1) {
		$comparray = array();

		for ($i = 1; $i < count($subjcomp); $i++)
			$comparray[] = $subjcomp[$i]->getFieldByName($INDICATEDVALSQFT[0]);

		$num = count($comparray);
		sort($comparray);

		if ($num % 2) {
			$median = $comparray[floor($num / 2)];
		} else {
			$median = ($comparray[$num / 2] + $comparray[$num / 2 - 1]) / 2;
		}
	}
	return number_format($median,2);
}

/**
 * @return mysqli
 */
function sqldbconnect()
{
	global $servername,$username,$password,$database,$dbport;

	// Create connection
    $mysqli = new mysqli($servername, $username, $password, $database, $dbport);

	
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

/**
 * @deprecated
 */
function tableLookup($id,$glbfield)
{
	global $ACTUALYEARBUILT,$SALEDATE,$SALEPRICE,$MKTLEVELERDETAILADJ;

	switch($glbfield)
	{
		case("Living Area"):
			return getLivingArea($id);
		case("High Value Improv MA RCN"):
			return getHVImpMARCN($id);
		case($ACTUALYEARBUILT[0]):
			return getYearBuilt($id);
		case($SALEDATE[0]):
			return getSaleDate($id);
		case($SALEPRICE[0]):
			return getSalePrice($id);
		case($MKTLEVELERDETAILADJ[0]):
			return getMktLevelerDetailAdj($id);
		default:
			return "table lookup failed";
	}
}

/**
 * @deprecated
 */
function getSaleDate($propid)
{
	global $TABLE_SALES_MERGED,$SALEDATE,$comparrsd;
	
	if(@$comparrsd[$propid] != null)
		return $comparrsd[$propid];

	$year = date("Y");
	$lastyear = $year -1;
	
	$query="SELECT $SALEDATE[2] FROM ". $TABLE_SALES_MERGED  . " WHERE prop_id=$propid AND (sale_date LIKE '%$year%' OR sale_date LIKE '%$lastyear%')";

	$result=doSqlQuery($query);
	$num=mysqli_num_rows($result);

	if($num==0)
	return "No Record Found";

	$row = mysqli_fetch_array($result);
	return $row[$SALEDATE[2]];
}

/**
 * @deprecated
 */
function getSalePrice($propid)
{
	global $TABLE_SALES_MERGED,$SALEPRICE,$comparrsp;
	
	if(@$comparrsp[$propid] != null)
		return $comparrsp[$propid];

	$year = date("Y");
	$lastyear = $year -1;

	$query="SELECT $SALEPRICE[2] FROM ". $TABLE_SALES_MERGED . " WHERE prop_id=$propid AND (sale_date LIKE '%$year%' OR sale_date LIKE '%$lastyear%')";

	//echo $query;
	$result=doSqlQuery($query);
	$num=mysqli_num_rows($result);

	if($num==0)
	{
		if(@$comparrsp[$propid] != null)
		return $comparrsp[$propid];
		else
		return "No Record Found";
	}

	$row = mysqli_fetch_array($result);
	return $row[$SALEPRICE[2]];
}

function setSaleInfo(propertyClass $compid,$prevyear,$instance=0,$compTable = null)
{
	global $TABLE_SALES_MERGED,$SALEDATE,$SALEPRICE,$debug,$debugquery,$SALESOURCE,$SALETYPE;

	$year = date("Y");

	if($compTable === null)
		$compTable = $TABLE_SALES_MERGED;
	
    $years = "sale_date LIKE '%".$year."%'";
    for($i=1; $i <= $prevyear; $i++){
        $yearsBack = $year - $i ;
        $years = $years . "OR sale_date LIKE '%".$yearsBack."%' ";
    }
	$query="SELECT sale_date,sale_price,source,sale_type FROM ". $compTable . " WHERE prop_id=".$compid->getPropID()." AND (".$years.")";


    if($debugquery) error_log("setSaleInfo:: query=".$query);
		
	$result=doSqlQuery($query);
	if($result === false)
		return $compid;
		
	$num=mysqli_num_rows($result);

	if($num==0)
		return "No Record Found";

	if($num > 1)
		error_log("Found multiple sales for propid:".$compid->getPropID());


	$tmpsalePrice = null;
	$tmpsaleDate = null;
    $rowNum = 0;
	while($row = mysqli_fetch_array($result)){
        if($rowNum == $instance){
            if($tmpsalePrice === null){
                $tmpsalePrice = $row[$SALEPRICE[2]];
                $tmpsaleDate = $row[$SALEDATE[2]];
                $tmpsaleSource = $row[$SALESOURCE[2]];
                $tmpSaleType = $row[$SALETYPE[2]];
            }
            else{
                if($tmpsalePrice== 0 || ($row[$SALEPRICE[2]] > 0 && $row[$SALEPRICE[2]] < $tmpsalePrice)){
                    $tmpsalePrice = $row[$SALEPRICE[2]];
                    $tmpsaleDate = $row[$SALEDATE[2]];
                    $tmpsaleSource = $row[$SALESOURCE[2]];
                    $tmpSaleType = $row[$SALETYPE[2]];
                }
            }
        }
        $rowNum++;
	}
	
	$compid->mSaleDate = $tmpsaleDate;
	$compid->setSalePrice($tmpsalePrice);
    $compid->mSaleSource = $tmpsaleSource;
    $compid->mSaleType = $tmpSaleType;
	return $compid;
}

function getLivingArea($propid)
{
	global $LIVINGAREA, $debug;

	$query="SELECT * FROM ". $LIVINGAREA["TABLE"] . " WHERE prop_id='$propid'";

	//	echo $query;
    if($debug) error_log("getLivingArea: query=".$query);

	$result=doSqlQuery($query);
	$num=mysqli_num_rows($result);

	if(!$result)
	return "No Value Found!";
	elseif($num > 1)
	return "UNEXPECTED ERROR:More then 1 result found";

	$row = mysqli_fetch_array($result);
	return $row[$LIVINGAREA[2]];

}

function getHVImpMARCN($propid){
	getHVImpMARCNwImp($propid,null);
}

function getHVImpMARCNwImp($propid,$primeImpId)
{
	global $HIGHVALIMPMARCN,$allowablema;
	$mafield = "Imprv_det_type_cd";

	//$query = "SELECT * FROM ".$HIGHVALIMPMARCN["TABLE"]." WHERE prop_id='$propid'";
	$subquery = "";

	$i=0;
	while($i < count($allowablema))
	{
		$subquery .= "imprv_det_type_cd='$allowablema[$i]'";
		if (++$i < count($allowablema))
		$subquery .= " OR ";
	}

	$query = "SELECT $HIGHVALIMPMARCN[2] FROM IMP_DET, SPECIAL_IMP
		WHERE IMP_DET.prop_id='$propid'
		AND ( " . $subquery . ")
		AND imprv_det_id = det_id
		AND IMP_DET.prop_id = SPECIAL_IMP.prop_id";
	
	if($primeImpId != null)
	{
		$query .= " AND IMP_DET.imprv_id = " . $primeImpId;
	}

	//echo "$query";
	$result=doSqlQuery($query);

	if(!$result)
	return "No Value Found!";

	$value=0;

	while($row = mysqli_fetch_array($result))
	{
		$value += $row[$HIGHVALIMPMARCN[2]];
	}
	return $value;

}

function getMktLevelerDetailAdjDelta($subj,$comp)
{
	global $MKTLEVELERDETAILADJ;

	return $subj[$MKTLEVELERDETAILADJ[0]] - $comp[$MKTLEVELERDETAILADJ[0]];

}

function getYearBuilt($propid)
{
	global $ACTUALYEARBUILT;
	$mafield = "Imprv_det_type_cd";
	$mafieldval = "1ST";

	$query="SELECT * FROM ". $ACTUALYEARBUILT["TABLE"]." WHERE prop_id='$propid' AND ".$mafield."='".$mafieldval."'";

	$result=doSqlQuery($query);

	if(!$result)
		return "No Value Found!";
		
	$num=mysqli_num_rows($result);
	
	//if($num > 1)
	//	return "UNEXPECTED ERROR:More then 1 result found";

	$value=0;

	$row = mysqli_fetch_array($result);
	return $row[$ACTUALYEARBUILT[2]];
}

function getLandValAdjDelta($subj,$comp)
{
	global $LANDVALUEADJ;

	return $subj[$LANDVALUEADJ[0]] - $comp[$LANDVALUEADJ[0]];

}

/**
 * Depricated use the one in the propertyClass
 * @param $subj
 * @param $comp
 * @return mixed
 */
function getLASizeAdjDelta($subj,$comp)
{
	global $LASIZEADJ,$HIGHVALIMPMARCNSQFT;
	$var1 = $subj[$LASIZEADJ[2]];
	$var2 = $comp[$LASIZEADJ[2]];
	$var3 = $subj[$HIGHVALIMPMARCNSQFT[2]];
	$constvar = .65;
	error_log("getLASizeAdjDelta: (".$var1."-".$var2.")*".$var3."*.65");
	return ($var1-$var2)*$var3*$constvar;
}


function getHVImpSqftDiff($subj,$comp)
{
	global $LASIZEADJ;

	$var1 = $subj[$LASIZEADJ[2]];
	$var2 = $comp[$LASIZEADJ[2]];

	return ($var1-$var2);

}

function hasDelta($class){
	global $landvaladjdelta,$classadjdelta,$goodadjdelta,$lasizeadjdelta,$mktlevelerdetailadjdelta,$segmentsadjdelta;
	global $LANDVALUEADJ,$CLASSADJ,$GOODADJ,$LASIZEADJ,$MKTLEVELERDETAILADJ,$SEGMENTSADJ;
	
	if($class === NULL)
		return NULL;
	
	switch($class)
	{
		case($LANDVALUEADJ[0]):
			return $landvaladjdelta;
		case($CLASSADJ[0]):
			return $classadjdelta;
		case($GOODADJ[0]):
			return $goodadjdelta;
		case($LASIZEADJ[0]):
			return $lasizeadjdelta;
		case($MKTLEVELERDETAILADJ[0]):
			return $mktlevelerdetailadjdelta;
		case($SEGMENTSADJ[0]):
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
		case($SEGMENTSADJ[0]):
			return $segmentsadjMultiRow;
		default:
			return NULL;
	}
}

function lookupProperty($propid)
{
	global $prop_table,$debug,$fieldsofinteresteq,$NETADJ,$INDICATEDVAL,$INDICATEDVALSQFT,$LANDVALUEADJ,$LANDVALUEADJB;
	
	$query="SELECT * FROM ". $prop_table . " WHERE prop_id='$propid'";
	if ($debug) echo $query;

	$result=doSqlQuery($query);
	$num=mysqli_num_rows($result);
	
	if($result){
		//$num_rows= $num; //$Attributes
		//$compcolumns = count($comparr);
		//$columns = $compcolumns +2; // + 1 for labels, +1 for subject
        //if($debug) echo "columns is ".$columns."<br/>";
		//$rows = count($fieldsofinteresteq) + 1;// +1 for header
		$postcalcfields = array();
		
		$currprop = null;
		while($row = mysqli_fetch_array($result))
		{
			if($row['prop_id'] == $_SESSION['subjsess']->getPropID())
			{
				$currprop = $_SESSION['subjsess'];
				$currprop->mSubj = true;
			}
			else{
				for($i=1;$i <= $_SESSION['numcomps'];$i++)
				{
					if($row['prop_id'] == $_SESSION['comp'.$i]->getPropID())
					{
						$currprop = $_SESSION['comp'.$i];
						$currprop->mSubj = false;
					}
				}
			}
			foreach($fieldsofinteresteq as $field)
			{
				if($debug) echo "field is " . $field[0] . " " . $field[2] ."<br/>";
				// Situs is an array field example
				if(is_array($field[2]))
				{
					$concatvar = "";
					foreach($field[2] as $element)
					{
						if($debug) echo "element is " . $element . "<br/>";
						if(strncmp('-',$element,1) == 0)
							$concatvar .= trim(substr($element,1)) . " ";
						else
							$concatvar .= trim($row[$element]) . " ";
						if($debug) echo "concatvar is " . $concatvar . "<br/>";
					}
					//$data[$dataindex][$field[0]] = $concatvar;
					$currprop->setField($field[0],$concatvar);
				}
				elseif((strncmp('-',$field[2],1) == 0))
				{
					//$data[$dataindex][$field[0]] = substr($field[2],1);
					$currprop->setField($field[0],substr($field[2],1));
				}
				elseif((strncmp("CALCULATED",$field[1],10) == 0))
				{
					$postcalcfields[] = $field;
				}
				elseif($field === NULL)
				{
					$currprop->setField($field[0],NULL);
				}
				elseif($field[1] == "TABLELOOKUP") // NEED ANOTHER TABLE LOOKUP
				{
					$currprop->setField($field[0], tablelookup($currprop->getPropID(),$field[0]));
				}
				elseif($field[1] == "GLOBALCALCULATED")
				{
					//NOOP for now
				}
				elseif ($field[1] != 'PROP')
				{
					//$data[$dataindex][$field[0]] 
					$currprop->setField($field[0], 'TBD');
				}
				else
				{//Must be dealing with a field in PROP table
					if($field[0] == $LANDVALUEADJ[0])
					{
						if(($row[$field[2]] == 0) &&
						   ($row[$LANDVALUEADJB[2]] != 0))
						   $currprop->setField($field[0],$row[$LANDVALUEADJB[2]]);
						else
						   $currprop->setField($field[0],$row[$field[2]]);
					}
					else
						$currprop->setField($field[0],$row[$field[2]]);
				}
				if(strcmp($currprop->getPropID(),$_SESSION['subjsess']->getPropID()) != 0 && 
				   ($delta = hasDelta($field[0])) != NULL)
				   {
				   	$func = "set".$delta;
				   	$currprop->$func($_SESSION['subjsess']);
				   }
			}
			
			foreach($postcalcfields as $field)
			{
				$currprop->setField($field[0],$currprop->$field[2]($_SESSION['subjsess']));
			}
			//Time for subj/comp compared calculation
			if($currprop != $_SESSION['subjsess']){
				$currprop->$NETADJ[2]();
				$currprop->$INDICATEDVAL[2]();
				$currprop->$INDICATEDVALSQFT[2]();
			}
		}
		
		if($debug)
		{
			echo "<br/>subj: " . var_dump($_SESSION['subjsess']) . "<br/>";
			for($i=1;$i <= $_SESSION['numcomps'];$i++)
				echo "<br/>data ".$i.": " . var_dump($_SESSION['comp'.$i]) . "<br/>";
		}
		
	}
}

/**
 * @param propertyClass $subj
 * @param propertyClass $currprop
 */
function calcDeltas($subj,$currprop)
{
	global $fieldsofinteresteq,$NETADJ,$INDICATEDVAL,$INDICATEDVALSQFT;
	
	foreach($fieldsofinteresteq as $field)
	{
		if(($delta = hasDelta($field[0])) != NULL)
		{
			$func = "set".$delta;
		   	$currprop->$func($subj);
		}
	}

	$currprop->setImpDets(ImpHelper::compareImpDetails_AddDelta($subj->getImpDets(), $currprop->getImpDets()));
    $tcadScore = new \TaxWizard\TcadScore();
    $tcadScore->setScore($subj, $currprop);
    $currprop->setTcadScore($tcadScore);

	$currprop->getNetAdj();
	$currprop->getIndicatedVal();
	$currprop->getIndicatedValSqft();
}

function getSubjProperty($propid){
    $property = getProperty($propid);
    $property->setisSubj(true);
    return $property;
}

/**
 * @param string $propid
 * @return propertyClass when successful
 *
 * Note: this function will NOT give delta information just basic and calculated data of property
 */
function getProperty($propid, $newMethod=true)
{
	if($newMethod){
		global $servername,$username,$password,$database,$dbport;
		$propDao = new PropertyDAO($servername, $username, $password, $database);
        $currprop = $propDao->getPropertyById($propid);
        $currprop->setisSubj(false);
		return $currprop;
	} else {
        throw new Exception("Attempted to use old getProperty method");
    }
}


/**
 * Retrieves an array of prop_class objects based on the neighborhood code
 * @param String $hood
 * @param queryContext $queryContext
 * @return Ambigous <propertyClass[], multitype:propertyClass >
 */
function getHoodList($hood, queryContext $queryContext){
	global $TABLE_SALES_MERGED,$NEIGHB,$PROPID, $debugquery;

	$year = date("Y");
	$hoodSearch = $NEIGHB["HOOD"] ."='$hood'";
	
	if($queryContext->multiHood){
		//Change the hood from something like K1005 to K10**
		$subHood = substr($hood,0,-2);
		$hoodSearch = $NEIGHB["HOOD"] ." LIKE '$subHood%'";
	}

	global $servername,$username,$password,$database,$dbport;
	$propDao = new PropertyDAO($servername, $username, $password, $database);
	return $propDao->getHoodProperties($hood, $queryContext);
}

/**
 * Takes a subject property and returns the comparables from the same neighborhood
 * where the square footage of the compared properties are within 25% of the subject (as of 2014 rules).
 * This function will also remove any properties of class 'XX'
 * @param queryContext queryContext
 * @return Array of comparable properties
 */
function findBestComps(propertyClass $subjprop, queryContext $queryContext)
{
	global $NEIGHB,$LIVINGAREA,$PROPID,$debug,$isEquityComp;
    $compsarray = array();

	//set global correctly, cuz prop_class uses this
	$isEquityComp = $queryContext->isEquityComp;


    //Now that we have all comps we only want ones where the sqft / LA is within 25%
    $subjsqft = $subjprop->getFieldByName($LIVINGAREA[0]);
    if($subjsqft == 0){
        error_log("findBestComps: subject sqft == 0 exiting");
        return $compsarray;
    }


    if($debug) error_log("findBestComps Start Memory >> ". memory_get_usage() . "\n");
	if($debug) echo "<br/>subj: " . var_dump($subjprop) . "<br/>";
	$comps = getHoodList($subjprop->getFieldByName($NEIGHB[0]),$queryContext);

    //Track for duplicates
    $compsSeen = array();

    if($debug) echo "<br/>walking ".count($comps)." potential comps<br/>";
	foreach($comps as $comp)
	{
        /* @var propertyClass $comp */
        if(!$queryContext->isEquityComp) {
            $compsCounts = array_count_values($compsSeen);
            if(array_key_exists($comp->getPropID(),$compsCounts)){
                //index off of the sale entry based on previously seen
                setSaleInfo($comp,$queryContext->prevYear,$compsCounts[$comp->getPropID()]);
            } else{
                setSaleInfo($comp,$queryContext->prevYear,0);
            }
        }

        if(addToCompsArray($comp,$subjprop,$queryContext)){
            if($debug) error_log("findBestComps: Adding ".$comp->getPropID(). " as comp::".$comp);
            $compsarray[] = $comp;
        } else {
            if($debug) error_log("findBestComps: Skipped adding ".$comp->getPropID()." as comp to ".$subjprop->getPropID());
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
    global $LIVINGAREA;
    $compsseen = array();

    if ($c->getPropID() == $subjprop->getPropID()) {
        error_log("addToCompsArray: Skipping Comp prop id matched subject:" . $c->getPropID());
        return false;
    }

    $subjsqft = $subjprop->getFieldByName($LIVINGAREA[0]);
	//sqftPercent is stored as int so convert to percentage
    $percentAllowed = $queryContext->sqftPercent *.01;
    $min = $subjsqft - ($percentAllowed* $subjsqft);
    $max = (1 + $percentAllowed) * $subjsqft;

    $sqft = $c->getFieldByName($LIVINGAREA[0]);

    if ($sqft < $min || $sqft > $max) {
        if ($queryContext->traceComps) error_log("addToCompsArray: " . $c->getPropID() . " removed as potential comp due to size min=" . $min . " max=" . $max . " size=" . $sqft);
        return false;
    }

    //Check sale type.
    //2014 : Can't include VU
    if (!$queryContext->isEquityComp && $queryContext->includeVu == false) {
        $badSaleTypes = "VU";
        if ($c->mSaleType == $badSaleTypes) {
            if ($queryContext->traceComps) error_log("addToCompsArray: Sale type was bad: " . $c->mSaleType);
            return false;
        }
    }


    // Check class
    //2013 : Can't include XX

    $badClass = "XX"; //don't include this type as it's not a good property to use
    if ($c->getClassCode() === null) {
        error_log("ERROR> addToCompsArray: Property has no class data: " . $c->getPropID());
        return false;
    }
    //Only review further if badClass string not found
    if ($c->getClassCode() === $badClass) {
        if ($queryContext->traceComps) error_log("addToCompsArray: Property has badclass " . $badClass);
        return false;
    }

    if ($queryContext->subClassRangeEnabled) {
        if (!fallsInsideClassRange($subjprop->getSubClass(), $c->getSubClass(), $queryContext->subClassRange)) {
            if ($queryContext->traceComps) error_log("addToCompsArray: failed to fall inside class range ");
            return false;
        }
    }

    if ($queryContext->percentGoodRangeEnabled) {
        if (!fallsWithinPercentGood($c, $subjprop, $queryContext->percentGoodRange)) {
            if ($queryContext->traceComps) error_log("addToCompsArray: failed to fall inside percent good range ");
            return false;
        }
    }

	if ($queryContext->limitToLessImps){
		$varCompImpCount = count(ImpHelper::getUniqueImpIds($c->getImpDets()));
		$varSubjImpCount = count(ImpHelper::getUniqueImpIds($subjprop->getImpDets()));
		if($varCompImpCount > $varSubjImpCount){
			if ($queryContext->traceComps) error_log("addToCompsArray: ". $c->getPropID() . " failed due to more subjects them prop");
			return false;
		}
	}

    calcDeltas($subjprop,$c);

    if ($queryContext->netAdjustEnabled){
        error_log("addToCompsArray: Filtering for net adjustment amount of " . $queryContext->netAdjustAmount);
        if(!fallsWithinNetAdjRange($c, $queryContext->netAdjustAmount)){
            error_log("addToCompsArray: failed to fall inside net adjustment range ");
            return false;
        }
    }

    if($queryContext->limitTcadScores){
        $tcadScore = $c->getTcadScore();
        if($tcadScore->getScore() < $queryContext->limitTcadScoresAmount){
            if($queryContext->traceComps){
                error_log("addToCompsArray: comp ".$c->getPropID(). " tcad score ". $tcadScore->getScore()
                    . " falls below threshold ". $queryContext->limitTcadScoresAmount);
            }
            return false;
        }
    }

    if($queryContext->trimIndicated){
        if(compareIndicatedVal($subjprop,$c)==1){
            if($queryContext->traceComps) error_log("addToCompsArray: Found comp ".$c->getPropID());
            return true;
        }
    } else {
        if($queryContext->traceComps) error_log("addToCompsArray: Found comp ".$c->getPropID());
        return true;
    }

    return false;
}

//Compares two properties for their indicated value
//Returns 0 if equal , -1 if prop1 is less the prop2, or 1 if prop1 > prop2
function compareIndicatedVal(propertyClass $prop1, propertyClass $prop2)
{
    global $debug;

    $prop1_Ind = intval($prop1->getIndicatedVal());
    if($prop1_Ind == 0)
        error_log("Error during comparison for indicated value of propid:".$prop1->getPropID());
    $prop2_Ind = intval($prop2->getIndicatedVal());
    if($prop2_Ind == 0)
        error_log("Error during comparison for indicated value of propid:".$prop2->getPropID());
    if($debug) echo "<br/>Comparing indicated values of".$prop1_Ind." and ".$prop2_Ind."<br/>";
    if ($prop1_Ind == $prop2_Ind) {
        return 0;
    }
    return ($prop1_Ind < $prop2_Ind) ? -1 : 1;
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

function fallsWithinPercentGood(propertyClass $comp, propertyClass $subjprop, $range){
    global $debug;
    $subjPercentGood = intval($subjprop->getGoodAdj());
    $compPercentGood = intval($comp->getGoodAdj());

    if($debug) error_log("fallsWithinPercentGood: subj ".$subjPercentGood." comp ". $compPercentGood . " range ". $range);

    if($compPercentGood < $subjPercentGood - $range){
        if($debug) error_log("fallsWithinPercentGood: Comp falls below range");
        return false;
    }

    if($compPercentGood > $subjPercentGood + $range){
        if($debug) error_log("fallsWithinPercentGood: Comp falls above range");
        return false;
    }

    return true;
}

function fallsWithinNetAdjRange(propertyClass $comp, $range){
    global $debug;
    $compNetAdj = intval($comp->getNetAdj());

    if($debug) error_log("fallsWithinNetAdjRange: comp ". $compNetAdj . " range ". $range);

    if($compNetAdj < $range * -1){
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

function generateArrayOfBestComps(propertyClass $property, queryContext $queryContext)
{
    $compsarray = findBestComps($property, $queryContext);

    if (sizeof($compsarray) == 0) {
        error_log("massreport: no comps found for " . $property->getPropID());
        return null;
    }

    if (!$queryContext->includeMls) {
        $compsarray = array_filter($compsarray, "isNotMLS");
    }

    if (sizeof($compsarray) == 0) {
        error_log("massreport: no comps found after MLS Sort for " . $property->getPropID());
    }

    error_log("massreport: found " . sizeof($compsarray) . " comp(s) for " . $property->getPropID());

    //resort to reset their index of any removed
    usort($compsarray, "compareIndicatedVal");

    $comp_min = MIN($queryContext->compsToDisplay, count($compsarray));
    $subjcomparray = array();
    $subjcomparray[0] = $property;

    for ($i = 0; $i < $comp_min; $i++) {
        $subjcomparray[$i + 1] = $compsarray[$i];

    }

    return $subjcomparray;
}