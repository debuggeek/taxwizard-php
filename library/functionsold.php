<?php

include 'definesold.php';
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

function getNetAdj($data)
{
	global $PROPID,$landvaladjdelta,$classadjdeltafunc,$goodadjdeltafunc,$lasizeadjdelta,$segadjdeltafunc,$mktlevelerdetailadjdelta,$neighbadjdelta;

	$var1 = intval($data[$landvaladjdelta]);
	$var2 = intval($data[$classadjdeltafunc]);
	$var3 = intval($data[$lasizeadjdelta]);
	$var4 = @intval($data[$goodadjdeltafunc]);
	$var5 = @intval($data[$mktlevelerdetailadjdelta]);
	//$var6 = intval($data[$neighbadjdelta]);
	//echo $var3." ".$var2."  ".$var1."<br/>";
	return ($var1+$var2 + $var3 + $var4 + $var5);// +$var6);
}

function getIndicatedVal($data)
{
	global $INDICATEDVAL,$SALEPRICE,$NETADJ,$MARKETVALUE,$isEquityComp;
	$var1 = $data[$SALEPRICE[0]];
	return $var1 + $data[$NETADJ[2]];
}

function getIndicatedValSqft($data)
{
	global $INDICATEDVAL,$LASIZEADJ;
	return number_format($data[$INDICATEDVAL[2]] / $data[$LASIZEADJ[2]],2);
}

function getMeanVal($subjcomp)
{
	global $INDICATEDVAL;
	$result = 0;
	$comps = count($subjcomp) -1;
	for($i=1;$i <= $comps; $i++)
	$result += $subjcomp[$i][$INDICATEDVAL[2]];

	$result = $result / $comps;
	return $result;
}

function getMeanValSqft($subjcomp)
{
	global $INDICATEDVALSQFT;
	$result = 0;
	$comps = count($subjcomp) -1;
	for($i=1;$i <= $comps; $i++)
	$result += $subjcomp[$i][$INDICATEDVALSQFT[2]];

	$result = $result / $comps;
	return $result;
}

function getMedianVal($subjcomp)
{
	global $INDICATEDVAL;

	$comparray = array();

	for($i=1;$i < count($subjcomp); $i++)
	$comparray[] = $subjcomp[$i][$INDICATEDVAL[2]];

	$num = count($comparray);
	sort($comparray);

	if ($num % 2) {
		$median = $comparray[floor($num/2)];
	} else {
		$median = ($comparray[$num/2] + $comparray[$num/2 - 1]) / 2;
	}
	return $median;
}

function getMedianValSqft($subjcomp)
{
	global $INDICATEDVALSQFT;

	$comparray = array();

	for($i=1;$i < count($subjcomp); $i++)
	$comparray[] = $subjcomp[$i][$INDICATEDVALSQFT[2]];

	$num = count($comparray);
	sort($comparray);

	if ($num % 2) {
		$median = $comparray[floor($num/2)];
	} else {
		$median = ($comparray[$num/2] + $comparray[$num/2 - 1]) / 2;
	}
	return $median;

}

function sqldbconnect()
{
	global $username,$password,$database;

	mysql_connect('localhost',$username,$password);
	@mysql_select_db($database) or die( "Unable to select database");

}

function tableLookup($id,$glbfield)
{
	global $SALEDATE,$SALEPRICE,$MKTLEVELERDETAILADJ;

	switch($glbfield)
	{
		case("Living Area"):
			return getLivingArea($id);
		case("High Value Improv MA RCN"):
			return getHVImpMARCN($id);
		case("Actual Year Built"):
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
	global $SALEDATE,$comparrsd;
	
	if(@$comparrsd[$propid] != null)
		return $comparrsd[$propid];

	sqldbconnect();
	$query="SELECT $SALEDATE[2] FROM ". $SALEDATE["TABLE"] . " WHERE prop_id=$propid";

	$result=mysql_query($query);
	if(!$result)
	{
		mysql_close();
		return "No Record Found";
	}
	$num=mysql_numrows($result);
	mysql_close();

	if($num==0)
	return "No Record Found";

	$row = mysql_fetch_array($result);
	return $row[$SALEDATE[2]];
}

function getSalePrice($propid)
{
	global $SALEPRICE,$comparrsp;
	
	if(@$comparrsp[$propid] != null)
		return $comparrsp[$propid];

	sqldbconnect();
	$query="SELECT $SALEPRICE[2] FROM ". $SALEPRICE["TABLE"] . " WHERE prop_id=$propid";

	//echo $query;
	$result=mysql_query($query);
	if(!$result)
	{
		mysql_close();
		return "No Record Found";
	}
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
			if(strcmp(trim($hoodval),trim($entryarray[0]))== 0)
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

function getHVImpMARCN($propid)
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

function getClassAdj($data)
{
	global $CLASSADJ,$HIGHVALIMPMARCN,$PROPID,$allowablema;
	$impids = array();
	$propid = $data[$PROPID[0]];

	$query = "SELECT det_class_code,det_subclass FROM IMP_DET, SPECIAL_IMP
			WHERE IMP_DET.prop_id='$propid'
			AND imprv_det_type_cd = '1ST' AND imprv_det_id = det_id AND IMP_DET.prop_id=SPECIAL_IMP.prop_id
			AND SPECIAL_IMP.det_use_unit_price LIKE 'T'";

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
}

function getUnitPrice($prop)
{
	global $UNITPRICE,$PROPID;
	$value = 0.0;

	$query = "SELECT ".$UNITPRICE["FIELD"]." FROM `".$UNITPRICE["TABLE"]."` WHERE `prop_id`=".$prop[$PROPID[0]]." AND `det_use_unit_price` LIKE 'T'";
		
	sqldbconnect();
	$result=mysql_query($query);
	mysql_close();
	if(!$result)
		return "No Hits returned";
	
	$num=mysql_numrows($result);
	
	if($num==0)
		return "No Value Found!";
		
	while($row = mysql_fetch_array($result))
	{
		$value += $row[$UNITPRICE["FIELD"]];
	}
		
	return $value;		
}
	
function getClassAdjDelta($subj,$comp)
{
	global $LIVINGAREA,$CLASSADJ,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,$PROPID;

	// 09 func
	$RCNfunc = $HIGHVALIMPMARCNSQFT[2];
	$prop1=(int)$subj[$PROPID[0]];
	$prop2=(int)$comp[$PROPID[0]];
	$var1 = (float)$RCNfunc($subj);
	$var2 = (float)$RCNfunc($comp);
	$var3 = $var1/$var2;
	$var4 = $var3 - 1;
	$result = $var4 * $comp[$HIGHVALIMPMARCN[0]];
	return $result;
	
	/*
	$var1 = (float)getUnitPrice($subj);
	$var2 = (float)getUnitPrice($comp);
	$var3 = $var1/$var2;
	$var4 = $var3 - 1;
	$result = $var4 * $var2 * $comp[$LIVINGAREA[0]];
	return $result;	
	*/
}

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

	//echo $var1." ".$var2." ".$var3." ".$var4;

	return ($var1 - $var2)/100 * ($var3-$var4);
}
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
		AND ( " . $subquery . ")";

	//echo "$query";
	sqldbconnect();
	$result=mysql_query($query);
	mysql_close();

	if(!$result)
	return "No Value Found!";

	$value=0;

	while($row = mysql_fetch_array($result))
	{
		$value += $row[$MKTLEVELERDETAILADJ[2]];
	}
	return $value;

}

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

function getNeighbMIADelta($subj,$comp)
{
	global $NEIGHBMIA;
	
	$var1 = (intval($subj[$NEIGHBMIA[2]])* .01);
	$var2 = (intval($comp[$NEIGHBMIA[2]])* .01);

	return number_format((($var1 - $var2)/$var2),3);
}

function getNeigbAdj($prop)
{
	global $NEIGHB;
	return $prop[$NEIGHB[0]];
}

function getNeigbAdjDelta($subj,$comp)
{
	global $SALEPRICE,$LANDVALUEADJ,$NEIGHBMIA;
	
	$var1 = $comp[$SALEPRICE[0]];
	$var2 = $comp[$LANDVALUEADJ[0]];
	$var3 = getNeighbMIADelta($subj,$comp);
	
	//if($debug) echo "getNeigbAdjDelta>> ((".$var1."-".$var2.") *".$var3.")<br>";
	
	return (($var1 - $var2) * $var3);
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
?>

