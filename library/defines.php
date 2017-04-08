<?php
//GLOBALS
include_once 'accountinfo.php';
$debug = false;
error_reporting(E_ALL);

date_default_timezone_set('America/Chicago');

$c = count($_GET);
$keys = array_keys($_GET);
$comparr = array();
$comparrsp = array();
$comparrsd = array();
$isEquityComp = false;

$prop_table="PROP";
$data = array();
$curryear = 2015;

// Format is one of the following
//		array(NAME, STRING REPRESENTATION, TABLE,FIELD)

$PROPID = array("NAME"=>"propId","STRING"=>"Property ID","PROP","prop_id");
$OWNER = array("NAME"=>"owner", "STRING"=>"Owner","PROP","py_owner_name");
$GEOID = array("NAME"=>"geoId", "STRING"=>"Geo ID","PROP","geo_id");
$SITUS = array("NAME"=>"situs", "STRING"=>"Situs","PROP",array("situs_num","situs_street_prefx","situs_street","situs_street_suffix","situs_unit","-TX","situs_zip"));
$OWNER = array("NAME"=>"owner", "STRING"=>"Property Owner","PROP","py_owner_name");

$NEIGHB = array("NAME"=>"neighborhood", "STRING"=>"Neighborhood","PROP","hood_cd","HOOD" => "hood_cd");
$NEIGHBMIA = array("NAME"=>"hoodMIA", "STRING"=>"Neighborhood Mass Improv Adj","CALCULATED","getNMIA","FIELD"=>"adjust_perc","TABLE"=>"SPECIAL_IMP");
$NEIGHBADJ = array("NAME"=>"hoodAdj", "STRING"=>"Neighborhood Adj","CALCULATED","getNeigbAdj");

$MARKETVALUE = array("NAME"=>"marketValue", "STRING"=>"Market Value","PROP","market_value");
$MARKETPRICESQFT = array("NAME"=>"marketPriceFoot", "STRING"=>"Market Price/SQFT","CALCULATED","getMrktSqft");
$LIVINGAREA = array("NAME"=>"livingArea", "STRING"=>"Living Area","CALCULATED", "getLivingArea");
$LIVINGAREAOLD = array("NAME"=>"livingAreaOld", "STRING"=>"Living Area","TABLELOOKUP","FIELD"=>"liv_area","TABLE"=>"SPECIAL_PROPDATA");

$SALEDATE = array("NAME"=>"saleDate", "STRING"=>"Sale Date","TABLELOOKUP","FIELD"=>"sale_date","TABLE"=>"SALES_MLS_MERGED");
$SALEPRICE = array("NAME"=>"salePrice", "STRING"=>"Sale Price","TABLELOOKUP","FIELD"=>"sale_price","TABLE"=>"SALES_MLS_MERGED");
$SALESOURCE = array("NAME"=>"saleSource", "STRING"=>"Sale Source","TABLELOOKUP","FIELD"=>"source","TABLE"=>"SALES_MLS_MERGED");
$SALETYPE = array("NAME"=>"saleType", "STRING"=>"Sale Type","TABLELOOKUP","FIELD"=>"sale_type","TABLE"=>"SALES_MLS_MERGED");
$SALEPRICESQFT = array("NAME"=>"salePriceSqft", "STRING"=>"Sale Price / SQFT","CALCULATED","getSPSqft");
$SALERATIO = array("NAME"=>"saleRatio", "STRING"=>"Sale Ratio", "CALCULATED", "getSaleRatio");
$ADJSALEPRICE = array("NAME"=>"adjSalePrice", "STRING"=>"Adj Sale Price", "CALCULATED", "getAdjSalePrice");
$SALETYPEANDCONF = array("NAME"=>"saleTypeConf", "STRING"=>"Sale Type - Conf Level", "CALCULATED", "getSaleTypeAndConf");

$IMPROVEMENTCNT = array("NAME"=>"impCount", "STRING"=>"Improvement Count","CALCULATED","getImpCount");
$HIGHVALIMPMARCN = array("NAME"=>"highValImpMARCN", "STRING"=>"High Value Improv MA RCN","CALCULATED","getHVImpMARCN");
$HIGHVALIMPMARCNSQFT = array("NAME"=>"highValImpMARCNSqft", "STRING"=>"High Value Improv MA RCN/SQFT","CALCULATED","getHVImpMARCNPerSQFT");
$COMPLETE = array("NAME"=>"complete", "STRING"=>"% Complete","-CONST","-100");

$LANDVALUEADJ =	array("NAME"=>"landValAdj", "STRING"=>"Land Value Adj","CALCULATED","getLandValueAdj");
$LANDVALUEADJB = array("NAME"=>"landValAdjB", "STRING"=>"Land Value Adj","TABLELOOKUP", "FIELD"=>"land_non_hstd_val", "TABLE"=>"PROP");

$STATECODE = array("NAME"=>"stateCode", "STRING"=>"State Code", "PROP", "stateCode");

$UNITPRICE = array("NAME"=>"unitPrice", "STRING"=>"Unit Price","TABLE" => "SPECIAL_IMP","FIELD"=>"det_unitprice");
$CLASSADJ = array("NAME"=>"classAdj", "STRING"=>"Class Unit Price Adj","CALCULATED","getClassAdj");
$ACTUALYEARBUILT = array("NAME"=>"yearBuilt", "STRING"=>"Year Built","CALCULATED","getYearBuilt");
$GOODADJ = array("NAME"=>"goodAdj", "STRING"=>"% Good Adj","CALCULATED","getGoodAdj","TABLE"=>"SPECIAL_IMP","FIELD"=>"det_base_deprec_perc");
$LASIZEADJ = array("NAME"=>"laSizeAdj", "STRING"=>"L/A Size Adj","CALCULATED","getLASizeAdj");
$HIGHVALIMPMASQFTDIFF = array("NAME"=>"highValImpMASqft", "STRING"=>"High Value Improv MA SQFT Diff","COMPCALCULATED","getHVImpSqftDiff");
$MKTLEVELERDETAILADJ = array("NAME"=>"mktLevelerDetailAdj", "STRING"=>"Mkt Leveler Detail Adj","CALCULATED","getMktLevelerDetailAdj");

$SEGMENTSADJ = array("NAME"=>"segAdj", "STRING"=>"Segments & Adj","CALCULATED","getSegAdj");
$SEGMENTSADJSIMPLE = array("NAME"=>"segAdjSimple", "STRING"=>"Segments & Adj", "CALCULATED", "getSegAdjSimple");

$NETADJ = array("NAME"=>"netAdj", "STRING"=>"Net Adjustment","COMPCALCULATED","getNetAdj");

$TCADSCORE = array("NAME"=>"tcadScore", "STRING"=>"TCAD Score", "CALCULATED", "getTcadScore");

$INDICATEDVAL = array("NAME"=>"indicatedVal", "STRING"=>"Indicated Value","COMPCALCULATED","getIndicatedVal");
$INDICATEDVALSQFT = array("NAME"=>"indicatedValSqft", "STRING"=>"Indicated Value / SQFT","COMPCALCULATED","getIndicatedValSqft");


$MEANVAL = array("NAME"=>"meanVal", "STRING"=>"Mean Value","GLOBALCALCULATED","setMeanVal", "KEY" => "getMeanVal");
$MEANVALSQFT = array("NAME"=>"meanValSqft", "STRING"=>"Mean Value / SQFT","GLOBALCALCULATED","setMeanValSqft", "KEY" => "getMeanValSqft");
$MEDIANVAL = array("NAME"=>"medianVal", "STRING"=>"Median Value","GLOBALCALCULATED","setMedianVal", "KEY" => "getMedianVal");
$MEDIANVALSQFT = array("NAME"=>"medianValSqft", "STRING"=>"Median Value / SQFT", "GLOBALCALCULATED","setMedianValSqft", "KEY" => "getMedianValSqft");

$AGENT = array("NAME"=>"agent", "STRING"=>"Agent","PROP","ca_agent_name");

$fieldsofinterestPre2015 = array($PROPID,$OWNER,$GEOID,$SITUS,$NEIGHB,NULL,$MARKETVALUE,$MARKETPRICESQFT,$LIVINGAREA,NULL,
						  $SALEDATE,$SALEPRICE,$SALEPRICESQFT,NULL,$IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,
						  $COMPLETE,NULL,$LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,$GOODADJ,$LASIZEADJ,$HIGHVALIMPMASQFTDIFF,
						  $MKTLEVELERDETAILADJ,$SEGMENTSADJ,NULL,$NETADJ,NULL,$INDICATEDVAL,$INDICATEDVALSQFT,NULL,$MEANVAL,
						  $MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT);

$fieldsofinterestwNeigh = array($PROPID,$GEOID,$SITUS,$NEIGHB,$NEIGHBMIA,NULL,$MARKETVALUE,$MARKETPRICESQFT,$LIVINGAREA,NULL,
						  $SALEDATE,$SALEPRICE,$SALEPRICESQFT,NULL,$IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,
						  $COMPLETE,NULL,$LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,$GOODADJ,$HIGHVALIMPMASQFTDIFF,
						  $MKTLEVELERDETAILADJ,$NEIGHBADJ,NULL,$NETADJ,NULL,$INDICATEDVAL,$INDICATEDVALSQFT,NULL,$MEANVAL,
						  $MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT);
						  
						  
$fieldsofinteresteqwSpace = array($PROPID,$SITUS,$NEIGHB,$NEIGHBMIA,NULL,$MARKETVALUE,$MARKETPRICESQFT,$LIVINGAREA,NULL,
						  $IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,
						  $COMPLETE,$LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,$GOODADJ,$LASIZEADJ,$HIGHVALIMPMASQFTDIFF,
						  $MKTLEVELERDETAILADJ,NULL,$NETADJ,NULL,$INDICATEDVAL,$INDICATEDVALSQFT,NULL,$MEANVAL,
						  $MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT);
						  
$fieldsofinteresteqPre2016 = array($PROPID,$GEOID,$OWNER,$NEIGHB,$SITUS, $MARKETVALUE,$MARKETPRICESQFT,NULL, $LIVINGAREA,
						  $IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,
						  $COMPLETE,$LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,$GOODADJ,$LASIZEADJ,$HIGHVALIMPMASQFTDIFF,
						  $MKTLEVELERDETAILADJ,$SEGMENTSADJ,$NETADJ,$INDICATEDVAL,$INDICATEDVALSQFT,$MEANVAL,
						  $MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT);
/*
 * 2016
 */
$fieldsofinterest_2016 = array($PROPID,$OWNER,$GEOID,$SITUS,NULL,$NEIGHB,NULL,$SALEDATE,$MARKETVALUE,$MARKETPRICESQFT,$SALEPRICE,
    $SALEPRICESQFT,NULL,$LANDVALUEADJB,$IMPROVEMENTCNT,$CLASSADJ,$GOODADJ,$LIVINGAREA,$ACTUALYEARBUILT,
    $SEGMENTSADJ,NULL,$NETADJ,NULL,$INDICATEDVAL,$INDICATEDVALSQFT,NULL,$MEANVAL,
    $MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT);

$fieldsofinteresteq_2016 = array($PROPID,$OWNER,$GEOID,$NEIGHB, $SITUS,$MARKETVALUE,$MARKETPRICESQFT,
	NULL,$LANDVALUEADJB,$IMPROVEMENTCNT,$CLASSADJ,$GOODADJ,$LIVINGAREA,$ACTUALYEARBUILT,
	$SEGMENTSADJ,NULL,$NETADJ,NULL,$INDICATEDVAL,$INDICATEDVALSQFT,NULL,$MEANVAL,
	$MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT);

/*
 * Current
 */
$fieldsofinterest = array($PROPID,$GEOID,$SITUS,NULL,$NEIGHB,$NEIGHBMIA,NULL,$SALERATIO, $SALEDATE,$MARKETVALUE,$SALEPRICE,
    $ADJSALEPRICE, $SALETYPE, $SALETYPEANDCONF,NULL,$LANDVALUEADJ, $STATECODE, $IMPROVEMENTCNT, $CLASSADJ, $GOODADJ, $COMPLETE, $LASIZEADJ,
    $LIVINGAREA, $ACTUALYEARBUILT, $MKTLEVELERDETAILADJ,
    $SEGMENTSADJSIMPLE, NULL,$NETADJ,NULL,$INDICATEDVAL,NULL,$MEDIANVAL);

$fieldsofinteresteq = array($PROPID,$GEOID,$NEIGHB,NULL,
    $NEIGHBMIA,$SITUS,$MARKETVALUE,NULL,
    $LANDVALUEADJB, $STATECODE, $IMPROVEMENTCNT, $CLASSADJ, $GOODADJ, $COMPLETE, NULL,
    $ACTUALYEARBUILT, $LASIZEADJ, $LIVINGAREA, $MKTLEVELERDETAILADJ, $SEGMENTSADJSIMPLE, NULL,
    $NETADJ,NULL,
    $INDICATEDVAL,NULL,
    $MEDIANVAL);

$fieldsofinterestprop = array($PROPID,$GEOID,$SITUS,$NEIGHB,$NEIGHBMIA,NULL,$MARKETVALUE,$MARKETPRICESQFT,$LIVINGAREA,NULL,
						  $IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,
						  $COMPLETE,NULL,$LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,$GOODADJ,$LASIZEADJ,$HIGHVALIMPMASQFTDIFF,
						  $MKTLEVELERDETAILADJ,NULL,$NETADJ,NULL,$INDICATEDVAL,$INDICATEDVALSQFT);	
	
$mafield = "Imprv_det_type_cd";
$allowablema = array("1/2","1ST","2ND","3RD","4TH","5TH","ADDL","ATRM","BELOW","CONC","DOWN","FBSMT","LOBBY","MEZZ","PBSMT","RSBLW","RSDN");
$EXCLUDED_IMPROVEMENT_CODES = array("SO");

$landvaladjdelta ="LandValAdjDelta";
$classadjdelta = "ClassAdjDelta";
$goodadjdelta = "GoodAdjDelta";
$lasizeadjdelta = "LASizeAdjDelta";
$mktlevelerdetailadjdelta =	"MktLevelerDetailAdjDelta";
$segmentsadjdelta = "SegAdjDelta";

$segmentsadjMultiRow = "getImpDets";

//TABLE NAMES
$TABLE_IMP_DET = "IMP_DET";
$TABLE_SPEC_IMP = "SPECIAL_IMP";
$TABLE_SALES = "SPECIAL_SALE_EX_CONF";
$TABLE_SALES_MERGED = "SALES_MLS_MERGED"; 
$TABLE_PROSPECT_LIST = "PROSPECT_LIST";
?>
