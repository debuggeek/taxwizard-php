<?php
$debug = false;
$debugquery = false;
include_once 'defines.php';
include_once 'prop_class.php';
//FUNCTIONS

function getMrktSqft($data)
{
	global $MARKETVALUE,$LIVINGAREA;
	return number_format($data[$MARKETVALUE[0]] / $data[$LIVINGAREA[0]],2);
}


function getSPSqft($data)
{
	global $SALEPRICE,$LIVINGAREA;
	$saleprice = $data[$SALEPRICE[0]];
	$livingarea = $data[$LIVINGAREA[0]];

	if(is_numeric($saleprice) && is_numeric($livingarea))
	return $saleprice / $livingarea;
	else
	return "ERROR";
}

function getHVImpSqft($data)
{
	global $HIGHVALIMPMARCN,$LIVINGAREA;

	return number_format($data[$HIGHVALIMPMARCN[0]] / $data[$LIVINGAREA[0]],2);
	return "funcgetHVImpSqft";
}

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
	
	$result = $result / $compCount;
	return number_format($result);
}

function getMeanValSqft($subjcomp)
{
	global $INDICATEDVALSQFT;
	$result = 0;
	$comps = count($subjcomp) -1;
	for($i=1;$i <= $comps; $i++)
		$result += $subjcomp[$i]->getFieldByName($INDICATEDVALSQFT[0]);

	$result = $result / $comps;
	return number_format($result,2);
}

function getMedianVal($subjcomp)
{
	global $INDICATEDVAL;

	$comparray = array();

	for($i=1;$i < count($subjcomp); $i++){
		$next = str_replace(",","",$subjcomp[$i]->getFieldByName($INDICATEDVAL[0]));
		$comparray[] = $next;
	}

	$num = count($comparray);
	sort($comparray);

	if ($num % 2) {
		$median = $comparray[floor($num/2)];
	} else {
		$median = ($comparray[$num/2] + $comparray[$num/2 - 1]) / 2;
	}
	return number_format($median);
}

function getMedianValSqft($subjcomp)
{
	global $INDICATEDVALSQFT;

	$comparray = array();

	for($i=1;$i < count($subjcomp); $i++)
	$comparray[] = $subjcomp[$i]->getFieldByName($INDICATEDVALSQFT[0]);

	$num = count($comparray);
	sort($comparray);

	if ($num % 2) {
		$median = $comparray[floor($num/2)];
	} else {
		$median = ($comparray[$num/2] + $comparray[$num/2 - 1]) / 2;
	}
	return number_format($median,2);

}

/**
 * @return link_identifier
 */
function sqldbconnect()
{
	global $username,$password,$database;

	$link = mysql_connect("localhost:8889",$username,$password);
	@mysql_select_db($database) or die( "Unable to select database");
	return $link;
}

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

function getSaleDate($propid)
{
	global $TABLE_SALES_MERGED,$SALEDATE,$comparrsd;
	
	if(@$comparrsd[$propid] != null)
		return $comparrsd[$propid];

	$year = date("Y");
	$lastyear = $year -1;
	
	sqldbconnect();
	$query="SELECT $SALEDATE[2] FROM ". $TABLE_SALES_MERGED  . " WHERE prop_id=$propid AND (sale_date LIKE '%$year%' OR sale_date LIKE '%$lastyear%')";

	$result=mysql_query($query);
	$num=mysql_numrows($result);
	mysql_close();

	if($num==0)
	return "No Record Found";

	$row = mysql_fetch_array($result);
	return $row[$SALEDATE[2]];
}

function getSalePrice($propid)
{
	global $TABLE_SALES_MERGED,$SALEPRICE,$comparrsp;
	
	if(@$comparrsp[$propid] != null)
		return $comparrsp[$propid];

	$year = date("Y");
	$lastyear = $year -1;

	sqldbconnect();
	$query="SELECT $SALEPRICE[2] FROM ". $TABLE_SALES_MERGED . " WHERE prop_id=$propid AND (sale_date LIKE '%$year%' OR sale_date LIKE '%$lastyear%')";

	//echo $query;
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	mysql_close();

	if($num==0)
	{
		if(@$comparrsp[$propid] != null)
		return $comparrsp[$propid];
		else
		return "No Record Found";
	}

	$row = mysql_fetch_array($result);
	return $row[$SALEPRICE[2]];
}

function setSaleDateAndPrice($compid, $compTable = null)
{
	global $TABLE_SALES_MERGED,$SALEDATE,$PROPID,$SALEPRICE,$debug,$debugquery;

	$year = date("Y");
	$lastyear = $year -1;

	if($compTable == null)
		$compTable = $TABLE_SALES_MERGED;
	
	sqldbconnect();
	$query="SELECT sale_date,sale_price FROM ". $compTable . " WHERE prop_id=".$compid->mPropID." AND (sale_date LIKE '%$year%' OR sale_date LIKE '%$lastyear%')";


	if($debugquery) echo("<br/> setSaleDateAndPrice query:".$query."<br/>");
		
	$result=mysql_query($query);
	if($result === false)
		return $compid;
		
	$num=mysql_numrows($result);
	mysql_close();

	if($num==0)
		return "No Record Found";
	if($num > 1)
		error_log("Found multiple sales for propid:".$compid->mPropID." finding lowest >0");
	
	$tmpsalePrice = null;
	$tmpsaleDate = null;
	while($row = mysql_fetch_array($result)){
		if($tmpsalePrice == null){
			$tmpsalePrice = $row[$SALEPRICE[2]];
			$tmpsaleDate = $row[$SALEDATE[2]];
		}
		else{
			if($tmpsalePrice== 0 || ($row[$SALEPRICE[2]] > 0 && $row[$SALEPRICE[2]] < $tmpsalePrice))
				$tmpsalePrice = $row[$SALEPRICE[2]];
				$tmpsaleDate = $row[$SALEDATE[2]];
		}
	}
	
	$compid->setField($SALEDATE[0],$tmpsaleDate);
	$compid->setField($SALEPRICE[0],$tmpsalePrice);
	return $compid;
}

/**
 * @param propertyClass $data
 * @return string|string|Ambigous <>|string
*/
function getNMIA($data)
{
	global $NEIGHBMIA,$NEIGHB,$PROPID;
	$hoodval = $data[$NEIGHB[0]];
	$prop_id = $data[$PROPID[0]];

	sqldbconnect();
	$query="SELECT * FROM ". $NEIGHBMIA["TABLE"] . " WHERE prop_id=$prop_id";

	$result=mysql_query($query);
	$num=mysql_numrows($result);
	mysql_close();

	if($num==0)
	return "No Value Found!";

	$row = mysql_fetch_array($result);
	$adjarray = explode(";",$row[$NEIGHBMIA["FIELD"]],100);
	if(count($adjarray) ==0)
	return "No Value Found!";

	foreach($adjarray as $entry)
	{
		$entryarray = explode(":",$entry,2);
		if(count($entryarray) != 0)
		{
			if(strcmp($hoodval,trim($entryarray[0]))== 0)
			return $entryarray[1];
		}
	}

	return "No Value Found!";
}
 

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
	sqldbconnect();
	$result=mysql_query($query);
	mysql_close();

	if(!$result)
	return "No Value Found!";

	$value=0;

	while($row = mysql_fetch_array($result))
	{
		$value += $row[$HIGHVALIMPMARCN[2]];
	}
	return $value;

}
/*MOVED TO CLASS
function getClassAdj($data)
{
	global $CLASSADJ,$HIGHVALIMPMARCN,$PROPID,$allowablema;
	$impids = array();
	$propid = $data[$PROPID[0]];

	$query = "SELECT det_class_code,det_subclass FROM IMP_DET, SPECIAL_IMP
			WHERE IMP_DET.prop_id='$propid'
			AND imprv_det_type_cd = '1ST' AND imprv_det_id = det_id AND IMP_DET.prop_id=SPECIAL_IMP.prop_id
			AND SPECIAL_IMP.det_use_unit_price LIKE 'T'";

	echo $query . "<BR/>";
	sqldbconnect();
	$result=mysql_query($query);
	$num=mysql_numrows($result);

	mysql_close();

	if(!$result)
	return "No Value Found!";
	//elseif($num > 1)
	//return "UNEXPECTED ERROR:More then 1 result found";

	$resultarray = mysql_fetch_array($result);

	return $resultarray[0].$resultarray[1];
}*/
/*
function getClassAdjDelta($subj,$comp)
{
	global $CLASSADJ,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,$PROPID;

	$RCNfunc = $HIGHVALIMPMARCNSQFT[2];
	$prop1=(int)$subj[$PROPID[0]];
	$prop2=(int)$comp[$PROPID[0]];
	$var1 = (float)$RCNfunc($subj);
	$var2 = (float)$RCNfunc($comp);
	$var3 = $var1/$var2;
	$var4 = $var3 - 1;
	$result = $var4 * $comp[$HIGHVALIMPMARCN[0]];
	return $result;
}
*/
function getGoodAdj($data)
{
	global $PROPID,$GOODADJ,$allowablema,$mafield;
	$goodfield = $GOODADJ["FIELD"];
	$goodtable = $GOODADJ["TABLE"];
	$propid = $data[$PROPID[0]];
	$imprvidtable = "IMP_DET";
	
	$subquery = "";
	$i=0;
	while($i < count($allowablema))
	{
		$subquery .= "$mafield = '$allowablema[$i]'";
		if (++$i < count($allowablema))
			$subquery .= " OR ";
	}
	$query = "SELECT $goodfield FROM $goodtable,$imprvidtable
		WHERE $imprvidtable.prop_id='$propid'
		AND $goodtable.prop_id='$propid'
		AND $imprvidtable.Imprv_det_id = $goodtable.det_id
		AND ( " . $subquery . ")";

	//echo "$query";
	sqldbconnect();
	$result=mysql_query($query);
	mysql_close();

	if(!$result)
		return "No Value Found!";

	$row = mysql_fetch_array($result);
	return $row[$goodfield];
}


function getGoodAdjDelta($subj,$comp)
{
	global $SALEPRICE,$MARKETVALUE,$LANDVALUEADJ,$GOODADJ,$isEquityComp;

	if($isEquityComp)
		$var1 = $comp[$MARKETVALUE[0]];
	else
		$var1 = $comp[$SALEPRICE[0]];
	$var2 = $comp[$LANDVALUEADJ[0]];
	$var3 = $subj[$GOODADJ[2]];
	$var4 = $comp[$GOODADJ[2]];

	if($var1 == 0 || $var2 == 0 || $var3 == 0 || $var4 ==0)
		echo "<BR>ERROR: getGoodAdjDelta has null value for var1:". $var1." var2:".$var2." var3:".$var3." var4:".$var4;

	return ($var1 - $var2)/100 * ($var3-$var4);
}

/*
function getMktLevelerDetailAdj($propid)
{
	global $MKTLEVELERDETAILADJ,$allowablema,$mafield;
	$table = $MKTLEVELERDETAILADJ["TABLE"];
	$imprvidtable = "IMP_DET";

	$subquery = "";

	$i=0;
	while($i < count($allowablema))
	{
		$subquery .= "imprv_det_type_cd != '$allowablema[$i]'";
		if (++$i < count($allowablema))
		$subquery .= " AND ";
	}

	$query = "SELECT $MKTLEVELERDETAILADJ[2] FROM $table,$imprvidtable 
		WHERE $imprvidtable.prop_id = '$propid' AND $table.prop_id = '$propid'
		AND $imprvidtable.Imprv_det_id = $table.det_id
		AND ( " . $subquery . ")" //AND IMP_DET.imprv_id = '$'";

	//echo "$query" . "<br>";
	sqldbconnect();
	$result=mysql_query($query);
	mysql_close();

	if(!$result)
	return "No Value Found!";
	
	$num=mysql_numrows($result);
	
	if($num == 0 )
		return "No Value Found!";

	$value=0;

	while($row = mysql_fetch_array($result))
	{
		$value += $row[$MKTLEVELERDETAILADJ[2]];
	}
	return $value;

}
*/

function getMktLevelerDetailAdjDelta($subj,$comp)
{
	global $MKTLEVELERDETAILADJ;

	return $subj[$MKTLEVELERDETAILADJ[0]] - $comp[$MKTLEVELERDETAILADJ[0]];

}

function getLASizeAdj($data)
{
	global $PROPID,$allowablema;
	$propid = $data[$PROPID[0]];
	$subquery = "";

	$i=0;
	while($i < count($allowablema))
	{
		$subquery .= "imprv_det_type_cd='$allowablema[$i]'";
		if (++$i < count($allowablema))
		$subquery .= " OR ";
	}

	$query = "SELECT det_area FROM IMP_DET, SPECIAL_IMP
			WHERE IMP_DET.prop_id='$propid'
			AND ( " . $subquery . ")
			AND imprv_det_id = det_id
			AND IMP_DET.prop_id = SPECIAL_IMP.prop_id";

	sqldbconnect();
	$result=mysql_query($query);
	$num=mysql_numrows($result);
		
	if(!$result)
	return "No Value Found!";

	$value=0;

	while($row = mysql_fetch_array($result))
	{
		$value += $row["det_area"];
	}

	return $value;

}

function getYearBuilt($propid)
{
	global $ACTUALYEARBUILT;
	$mafield = "Imprv_det_type_cd";
	$mafieldval = "1ST";

	sqldbconnect();
	$query="SELECT * FROM ". $ACTUALYEARBUILT["TABLE"]." WHERE prop_id='$propid' AND ".$mafield."='".$mafieldval."'";

		$result=mysql_query($query);

	if(!$result)
		return "No Value Found!";
		
	$num=mysql_numrows($result);
	
	//if($num > 1)
	//	return "UNEXPECTED ERROR:More then 1 result found";

	$value=0;

	$row = mysql_fetch_array($result);
	return $row[$ACTUALYEARBUILT[2]];
}

function getLandValAdjDelta($subj,$comp)
{
	global $LANDVALUEADJ;

	return $subj[$LANDVALUEADJ[0]] - $comp[$LANDVALUEADJ[0]];

}


function getLASizeAdjDelta($subj,$comp)
{
	global $LASIZEADJ,$HIGHVALIMPMARCNSQFT;
	$var1 = $subj[$LASIZEADJ[2]];
	$var2 = $comp[$LASIZEADJ[2]];
	$var3 = $subj[$HIGHVALIMPMARCNSQFT[2]];
	$constvar = .65;
	//echo $var1." ".$var2." ".$var3." ";
	return ($var1-$var2)*$var3*$constvar;
}


function getHVImpSqftDiff($subj,$comp)
{
	global $LASIZEADJ;

	$var1 = $subj[$LASIZEADJ[2]];
	$var2 = $comp[$LASIZEADJ[2]];

	return ($var1-$var2);

}

function is_NumberField($fieldname)
{
	global $MARKETVALUE,$MARKETPRICESQFT,$LIVINGAREA,$SALEPRICE,$SALEPRICESQFT,
	$IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,
	$LANDVALUEADJ,$LASIZEADJ,$HIGHVALIMPMASQFTDIFF,$MKTLEVELERDETAILADJ,$SEGMENTSADJ;

	$numberfields = array($MARKETVALUE[0],$MARKETPRICESQFT[0],$LIVINGAREA[0],
	$SALEPRICE[0],$SALEPRICESQFT[0],
	$HIGHVALIMPMARCN[0],$HIGHVALIMPMARCNSQFT[0],
	$LANDVALUEADJ[0],$LASIZEADJ[0],$HIGHVALIMPMASQFTDIFF[0],$SEGMENTSADJ[0]);//$IMPROVEMENTCNT[0],$MKTLEVELERDETAILADJ[0]
	//	echo var_dump($numberfields);
	return in_array($fieldname,$numberfields);
}

function hasDelta($class){
	global $landvaladjdelta,$classadjdelta,$goodadjdelta,$lasizeadjdelta,$mktlevelerdetailadjdelta,$segmentsadjdelta;
	global $LANDVALUEADJ,$CLASSADJ,$GOODADJ,$LASIZEADJ,$MKTLEVELERDETAILADJ,$SEGMENTSADJ;
	
	if($class == NULL)
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
			return NULL;
	}
}

function lookupProperty($propid)
{
	global $table,$debug,$fieldsofinteresteq,$NETADJ,$INDICATEDVAL,$INDICATEDVALSQFT,$LANDVALUEADJ,$LANDVALUEADJB;
	
	$query="SELECT * FROM ". $table . " WHERE prop_id='$propid'";
	if ($debug) echo $query;

	sqldbconnect();
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	mysql_close();
	
	if($result){
		//$num_rows= $num; //$Attributes
		//$compcolumns = count($comparr);
		//$columns = $compcolumns +2; // + 1 for labels, +1 for subject
		//$rows = count($fieldsofinteresteq) + 1;// +1 for header
		$postcalcfields = array();
	
		if($debug) echo "columns is ".$columns."<br/>";
		
		$currprop = null;
		while($row = mysql_fetch_array($result))
		{
			if($row['prop_id'] == $_SESSION['subjsess']->mPropID)
			{
				$currprop = $_SESSION['subjsess'];
				$currprop->mSubj = true;
			}
			else{
				for($i=1;$i <= $_SESSION['numcomps'];$i++)
				{
					if($row['prop_id'] == $_SESSION['comp'.$i]->mPropID)
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
				elseif($field == NULL)
				{
					$currprop->setField($field[0],NULL);
				}
				elseif($field[1] == "TABLELOOKUP") // NEED ANOTHER TABLE LOOKUP
				{
					$currprop->setField($field[0], tablelookup($currprop->mPropID,$field[0]));
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
				if(strcmp($currprop->mPropID,$_SESSION['subjsess']->mPropID) != 0 && 
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
	$currprop->$NETADJ[2]();
	$currprop->$INDICATEDVAL[2]();
	$currprop->$INDICATEDVALSQFT[2]();
}

/**
 * @param string $propid
 * @return NULL when error | propertyClass when successful
 * 
 * Note: this function will NOT give delta information just basic and calculated data of property
 */
function getProperty($propid)
{
	global $table,$debug,$fieldsofinteresteq,$NETADJ,$INDICATEDVAL,$INDICATEDVALSQFT,$LANDVALUEADJ,$LANDVALUEADJB;
	
	$currprop = new propertyClass();
	
	$query="SELECT * FROM ". $table . " WHERE prop_id='$propid'";
	if ($debug) echo $query;

	sqldbconnect();
	$result=mysql_query($query);
	mysql_close();
	if(!$result)
	{
		echo "Bad query:". $query;
		return NULL;
	}
	$num=mysql_numrows($result);

	if($num > 1)
	{
		echo "ERROR: More then one match found for property";
		return NULL;
	}
	
	$postcalcfields = array();
	$currprop->mPropID= $propid;
	while($row = mysql_fetch_array($result))
	{
		foreach($fieldsofinteresteq as $field)
		{
			if($debug) echo "field is " . $field[0] . " " . $field[2] ."<br/>";
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
				$currprop->setField($field[0],$concatvar);
			}
			elseif((strncmp('-',$field[2],1) == 0))
			{
				$currprop->setField($field[0],substr($field[2],1));
			}
			elseif((strncmp("CALCULATED",$field[1],10) == 0))
			{
				$postcalcfields[] = $field;
			}
			elseif($field == NULL)
			{
				$currprop->setField($field[0],NULL);
			}
			elseif($field[1] == "TABLELOOKUP") // NEED ANOTHER TABLE LOOKUP
			{
				$currprop->setField($field[0], tablelookup($currprop->mPropID,$field[0]));
			}
			elseif($field[1] == "GLOBALCALCULATED")
			{
				//NOOP for now
			}
			elseif ($field[1] != 'PROP')
			{
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
		}
		foreach($postcalcfields as $field)
		{
			$currprop->setField($field[0],$currprop->$field[2]());
		}
	}
	return $currprop;
}

/**
 * @param String $id : PropertyID
 * @param Array $fieldsofinterest : array of Fields
 * @return unknown_type
 */
function lookupPropID($id,$fieldsofinterest){
	global $debug;
	$table="PROP";
	$currprop = $_SESSION['target'];


	sqldbconnect();
	$query="SELECT * FROM ". $table . " WHERE prop_id='$id'";

	if ($debug) echo $query;

	$result=mysql_query($query);
	$num=mysql_numrows($result);
	mysql_close();


	if($result){
		$num_rows= $num; //$Attributes
		while($row = mysql_fetch_array($result))
		{
			foreach($fieldsofinterest as $field)
			{
				if($debug) echo "field is " . $field[0] . " " . $field[2] ."<br/>";
				if(is_array($field[2]))
				{
					$concatvar = "";
					foreach($field[2] as $element)
					{
						if($debug) echo "element is " . $element . "<br/>";
						if(strncmp('-',$element,1) == 0)
						$concatvar .= substr($element,1) . " ";
						else
						$concatvar .= $row[$element] . " ";
						if($debug) echo "concatvar is " . $concatvar . "<br/>";
					}
					$currprop->setField($field[0],$concatvar);
				}
			}

		}
	}
}

function getHoodListBounded($hood,$sqft,$limit){
	/*
	 * This func currently bounds 20% of sqft
	 */
	global $NEIGHB;
	$min = $sqft * .8;
	$max = $sqft * 1.2;
	
	// SELECT * FROM PROP, SPECIAL_PROPDATA WHERE PROP.hood_cd='R2004' AND SPECIAL_PROPDATA.liv_area BETWEEN 3359.2 AND 5038.8
	$query="SELECT * FROM ". $NEIGHB[1] . ", SPECIAL_PROPDATA WHERE ". $NEIGHB[1].".".$NEIGHB[2] ."='$hood' AND SPECIAL_PROPDATA.liv_area BETWEEN " . $min . " AND " . $max;
	#$query = "SELECT * FROM PROP, SPECIAL_PROPDATA WHERE PROP.hood_cd ='R2004' AND SPECIAL_PROPDATA.liv_area BETWEEN 3359 AND 5038 LIMIT 100;";
	if(is_Numeric($limit))
		$query = $query . " LIMIT " . intval($limit);
		
	return getHoodListQuery($query);
}
/*
function getHoodList($hood,$isEquity,$limit){
	global $NEIGHB,$MARKETVALUE,$LIVINGAREA,$PROPID,$debug;
	
	$query="SELECT * FROM ". $NEIGHB[1] . " WHERE ". $NEIGHB["HOOD"] ."='$hood'";

	if(is_Numeric($limit))
		$query = $query . " LIMIT " . intval($limit);
		
	return getHoodListQuery($query,false);
}
*/
/**
 * Retrieves an array of prop_class objects based on the neighborhood code
 * @param String $hood
 * @param Boolean $isEquity
 * @param int $limit - Limit on number of elements returned
 * @param String $compTable - Table to compare with
 * @return Ambigous <propertyClass[], multitype:propertyClass >
 */
function getHoodList($hood,$isEquity,$limit,$compTable = NULL,$multihood=FALSE){
	global $TABLE_SALES_MERGED,$NEIGHB,$MARKETVALUE,$LIVINGAREA,$PROPID,$debug;

	$year = date("Y");
	$lastyear = $year -1;
	$hoodSearch = $NEIGHB["HOOD"] ."='$hood'";
	
	if($compTable == NULL)
		$compTable = $TABLE_SALES_MERGED;
	
	if($multihood){
		//Change the hood from something like K1005 to K10**
		$subHood = substr($hood,0,-2);
		$hoodSearch = $NEIGHB["HOOD"] ." LIKE '$subHood%'";
	}
	

	if($isEquity)
		$query="SELECT * FROM ". $NEIGHB[1] . " WHERE ". $hoodSearch;
	else{
		$query="SELECT ". $PROPID[1].".".$PROPID[2] .",sale_price".
				" FROM ". $NEIGHB[1] ."," . $compTable . " AS s " .
				" WHERE ". $hoodSearch . 
					" AND (sale_date LIKE '%".$lastyear."%' OR sale_date LIKE '%".$year."%') ".
					" AND sale_price>0 ".
					" AND PROP.prop_id = s.prop_id";
		//SELECT PROP.prop_id,hood_cd,sale_price FROM PROP,SPECIAL_SALE_EX_CONF WHERE PROP.hood_cd='R2004' AND SPECIAL_SALE_EX_CONF.sale_price>0 LIMIT 10
	}
	if(is_Numeric($limit))
		$query = $query . " LIMIT " . intval($limit);

	return getHoodListQuery($query,$isEquity);
}

/**
 * Retrieves an array of prop_class elements based on the passed in query
 * @param String $query
 * @param Boolean $isEquity
 * @return propertyClass[]
 */

function getHoodListQuery($query,$isEquity){
	global $NEIGHB,$MARKETVALUE,$LIVINGAREA,$PROPID,$debug,$SALEPRICE;
	$breakcount = 100;
	error_log("getHoodListQuery Start Memory >> ". memory_get_usage() . "\n");
	if($isEquity)
		$fieldsofinterest = array($PROPID,$MARKETVALUE);
	else
		$fieldsofinterest = array($PROPID,$SALEPRICE);
	$hood_props = array();
	
	if ($debug) echo $query;

	sqldbconnect();
	$result=mysql_query($query);
	mysql_close();
	
	$num=mysql_numrows($result);
	
	//if($num > $breakcount)
	//	echo "<BR>".$num." Exceeds ".$breakcount." truncating results<BR>".PHP_EOL;
		
	if($result){
		$num_rows= $num; //$Attributes
		$i = 0;
		while($row = mysql_fetch_array($result)){
			$currprop = new propertyClass;
			foreach($fieldsofinterest as $field)
			{
				$currprop->setField($field[0],$row[$field[2]]);
			}
			//Removes any properties that have a bad living area value
			$val = getLivingArea($currprop->mPropID);
			if ($val > 0){
				$currprop->setField($LIVINGAREA[0],$val);
				$hood_props[] = $currprop;
			}
			else{
				if($debug)
					echo "<br>Error on propid:".$currprop->mPropID."...removing due to LivingArea of ".$currprop->mLivingArea."<br>".PHP_EOL;
				else
					error_log("Error on propid".$currprop->mPropID."...removing due to LivingArea of ".$currprop->mLivingArea.PHP_EOL);
			}
			$i++;
			//if($i > $breakcount)
			//	break;
		}
	}
	error_log("getHoodListQuery End Memory >> ". memory_get_usage() . "\n");
	/*
	//Removes any properties that have a bad living area value
	foreach($hood_props as $key => $prop){
		$val = getLivingArea($prop->mPropID);
		if ($val > 0)
			$prop->setField($LIVINGAREA[0],$val);
		else{
			if($debug)
				echo "<br>Error on propid:".$prop->mPropID."...removing due to LivingArea of ".$prop->mLivingArea."<br>".PHP_EOL;
			else
				error_log("Error on propid".$prop->mPropID."...removing due to LivingArea of ".$prop->mLivingArea.PHP_EOL);
			
			unset($hood_props[$key]);
		}
			
	}
	*/
	return $hood_props;
}

//Compares two properties for their indicated value
//Returns 0 if equal , -1 if prop1 is less the prop2, or 1 if prop1 > prop2
function cmpProp(propertyClass $prop1,propertyClass $prop2)
{
	global $INDICATEDVAL,$MARKETVALUE;
	
	$prop1_Ind = intval($prop1->getIndicatedVal());
	if($prop1_Ind == 0)
		error_log("Error during comparison for indicated value of propid:".$prop1->mPropID);
	$prop2_Ind = intval($prop2->getIndicatedVal());
	if($prop2_Ind == 0)
		error_log("Error during comparison for indicated value of propid:".$prop2->mPropID);
	
    if ($prop1_Ind == $prop2_Ind) {
        return 0;
    }
    return ($prop1_Ind < $prop2_Ind) ? -1 : 1;
}

/**
 * Takes a subject property and returns the comparables from the same neighborhood
 * where the square footage of the compared properties are within 20% of the subject.
 * This function will also remove any properties of class 'XX'
 * @param String subject property
 * @param Boolean True if doing equity compare
 * @param (Optional) Boolean Trim out properties > then subject
 * @param (Optional) String SQL Table you wish to use to find comps
 * @return Array of comparable properties
 */
function findBestComps($subjprop,$isEquity,$trimIndicated = false,$compTable = NULL,$multihood=false)
{
	global $NEIGHB,$LIVINGAREA,$PROPID,$debug,$isEquityComp;
	//set global correctly, cuz prop_class uses this
	$isEquityComp = $isEquity;
	error_log("findBestComps Start Memory >> ". memory_get_usage() . "\n");
	if($debug) echo "<br/>subj: " . var_dump($subjprop) . "<br/>";
	$comps = getHoodList($subjprop->getFieldByName($NEIGHB[0]),$isEquity,NULL,$compTable,$multihood);

	//Now that we have all comps we only want ones where the sqft / LA is within 20%
	$subjsqft = $subjprop->getFieldByName($LIVINGAREA[0]);
	$min = .8 * $subjsqft;
	$max = 1.2 * $subjsqft;
	$compsarray = array();
	$compsseen = array();
	foreach($comps as $comp)
	{
		$c = getProperty($comp->getFieldByName($PROPID[0]));
		$sqft = $c->getFieldByName($LIVINGAREA[0]);
		if($sqft > $min && $sqft < $max)
		{
			$badClass = "XX"; //don't include this type as it's not a good property to use
			$classadj = $c->getClassAdj();
			$pos = stripos($classadj,$badClass);
			if($pos === false){
				if(!$isEquity) {
					setSaleDateAndPrice($c,$compTable);
				}
				if($c->mPropID != $subjprop->mPropID){
					$key = array_search($c->mPropID,$compsseen);
					if($key == false){
						calcDeltas($subjprop,$c);
						if($trimIndicated){
							if(cmpProp($subjprop,$c)==1){
								//only return if the comp is less then the subject
								$compsarray[] = $c;
							}
						} else {
							$compsarray[] = $c;
						}
						$compsseen[] = $c->mPropID;
					}
				}
			}else
				if($debug) echo "<br/>Stripped property due to badclass ".$badClass;
		}
	}
	if ($debug) echo "compsarry count: ".count($compsarray);
	return $compsarray;
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
	$sqlResult = executeQuery($query);
		
	if(!$sqlResult){
		error_log("putPropHistory>> query failed:" . $query);
	}
}

/**
 * executeQuery
 * @param (String) $query
 * @return resource|boolean 
 */
function executeQuery($query){
	$link = sqldbconnect();
	$result=mysql_query($query) or die(error_log("Unable to update : " . mysql_error()));
	if (mysql_errno()) {
		$error = "MySQL error ".mysql_errno().": ".mysql_error()."  When executing:$query\n";
		error_log($error,"mySQL");
	}
	error_log(mysql_info($link));
	mysql_close();
	return $result;
}