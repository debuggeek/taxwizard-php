<?php
//GLOBALS
include 'accountinfo.php';
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
$curryear = 2009;

// Format is one of the following
//		array(NAME,TABLE,FIELD)
//		array(NAME,

$PROPID = array("Property ID","PROP","prop_id");
$OWNER = array("Owner","PROP","py_owner_name");
$GEOID = array("Geo ID","PROP","geo_id");
$SITUS = array("Situs","PROP",array("situs_num","situs_street_prefx","situs_street","situs_street_suffix","situs_unit","-TX","situs_zip"));
$NEIGHB = array("Neighborhood","PROP","hood_cd","HOOD" => "hood_cd");
$OWNER = array("Property Owner","PROP","py_owner_name");
$NEIGHBMIA = array("Neighborhood Mass Improv Adj","CALCULATED","getNMIA","FIELD"=>"adjust_perc","TABLE"=>"SPECIAL_IMP");

$MARKETVALUE = array("Market Value","PROP","market_value");
$MARKETPRICESQFT = array("Market Price/SQFT","CALCULATED","getMrktSqft");
$LIVINGAREA = array("Living Area","TABLELOOKUP","liv_area","TABLE"=>"SPECIAL_PROPDATA");

$SALEDATE = array("Sale Date","TABLELOOKUP","sale_date","TABLE"=>"SALES_MLS_MERGED");
$SALEPRICE = array("Sale Price","TABLELOOKUP","sale_price","TABLE"=>"SALES_MLS_MERGED");
$SALESOURCE = array("Sale Source","TABLELOOKUP","source","TABLE"=>"SALES_MLS_MERGED");
$SALETYPE = array("Sale Type","TABLELOOKUP","sale_type","TABLE"=>"SALES_MLS_MERGED");
$SALEPRICESQFT = array("Sale Price / SQFT","CALCULATED","getSPSqft");

$IMPROVEMENTCNT = array("Imp Count","CALCULATED","getImpCount");
$HIGHVALIMPMARCN = array("High Value Improv MA RCN","CALCULATED","getHVImpMARCN");
$HIGHVALIMPMARCNSQFT = array("High Value Improv MA RCN/SQFT","CALCULATED","getHVImpMARCNPerSQFT");
$COMPLETE = array("% Complete","-CONST","-100");

$LANDVALUEADJ =	array("Land Value Adj","CALCULATED","getLandValueAdj");
$LANDVALUEADJB =	array("Land Value Adj","PROP","land_non_hstd_val");
$UNITPRICE = array("Unit Price","TABLE" => "SPECIAL_IMP","FIELD"=>"det_unitprice");
$CLASSADJ = array("Class Adj","CALCULATED","getClassAdj");
$ACTUALYEARBUILT = array("Year Built","CALCULATED","getYearBuilt");
$GOODADJ = array("% Good Adj","CALCULATED","getGoodAdj","TABLE"=>"SPECIAL_IMP","FIELD"=>"det_base_deprec_perc");
$LASIZEADJ = array("L/A Size Adj","CALCULATED","getLASizeAdj");
$HIGHVALIMPMASQFTDIFF = array("High Value Improv MA SQFT Diff","COMPCALCULATED","getHVImpSqftDiff");
$MKTLEVELERDETAILADJ = array("Mkt Leveler Detail Adj","CALCULATED","getMktLevelerDetailAdj");
$SEGMENTSADJ = array("Segments & Adj","CALCULATED","getSegAdj");
$NEIGHBADJ = array("Neighborhood Adj","CALCULATED","getNeigbAdj");

$NETADJ = array("Net Adjustment","COMPCALCULATED","getNetAdj");

$INDICATEDVAL = array("Indicated Value","COMPCALCULATED","getIndicatedVal");
$INDICATEDVALSQFT = array("Indicated Value / SQFT","COMPCALCULATED","getIndicatedValSqft");


$MEANVAL = array("Mean Value","GLOBALCALCULATED","setMeanVal");
$MEANVALSQFT = array("Mean Value / SQFT","GLOBALCALCULATED","setMeanValSqft");
$MEDIANVAL = array("Median Value","GLOBALCALCULATED","setMedianVal");
$MEDIANVALSQFT = array("Median Value / SQFT", "GLOBALCALCULATED","setMedianValSqft");

$AGENT = array("Agent","PROP","ca_agent_name");

$fieldsofinterest = array($PROPID,$OWNER,$GEOID,$SITUS,$NEIGHB,NULL,$MARKETVALUE,$MARKETPRICESQFT,$LIVINGAREA,NULL,
						  $SALEDATE,$SALEPRICE,$SALEPRICESQFT,NULL,$IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,
						  $COMPLETE,NULL,$LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,$GOODADJ,$LASIZEADJ,$HIGHVALIMPMASQFTDIFF,
						  $MKTLEVELERDETAILADJ,$SEGMENTSADJ,NULL,$NETADJ,NULL,$INDICATEDVAL,$INDICATEDVALSQFT,NULL,$MEANVAL,
						  $MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT);	

$fieldsofinterestwNeigh = array($PROPID,$GEOID,$SITUS,$NEIGHB,$NEIGHBMIA,NULL,$MARKETVALUE,$MARKETPRICESQFT,$LIVINGAREA,NULL,
						  $SALEDATE,$SALEPRICE,$SALEPRICESQFT,NULL,$IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,
						  $COMPLETE,NULL,$LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,$GOODADJ,$LASIZEADJ,$HIGHVALIMPMASQFTDIFF,
						  $MKTLEVELERDETAILADJ,$NEIGHBADJ,NULL,$NETADJ,NULL,$INDICATEDVAL,$INDICATEDVALSQFT,NULL,$MEANVAL,
						  $MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT);
						  
						  
$fieldsofinteresteqwSpace = array($PROPID,$SITUS,$NEIGHB,$NEIGHBMIA,NULL,$MARKETVALUE,$MARKETPRICESQFT,$LIVINGAREA,NULL,
						  $IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,
						  $COMPLETE,$LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,$GOODADJ,$LASIZEADJ,$HIGHVALIMPMASQFTDIFF,
						  $MKTLEVELERDETAILADJ,NULL,$NETADJ,NULL,$INDICATEDVAL,$INDICATEDVALSQFT,NULL,$MEANVAL,
						  $MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT);
						  
$fieldsofinteresteq = array($PROPID,$GEOID,$SITUS,$OWNER,$NEIGHB,$MARKETVALUE,$MARKETPRICESQFT,$LIVINGAREA,
						  $IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,
						  $COMPLETE,$LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,$GOODADJ,$LASIZEADJ,$HIGHVALIMPMASQFTDIFF,
						  $MKTLEVELERDETAILADJ,$SEGMENTSADJ,$NETADJ,$INDICATEDVAL,$INDICATEDVALSQFT,$MEANVAL,
						  $MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT);	
						  
$fieldsofinterestprop = array($PROPID,$GEOID,$SITUS,$NEIGHB,$NEIGHBMIA,NULL,$MARKETVALUE,$MARKETPRICESQFT,$LIVINGAREA,NULL,
						  $IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,
						  $COMPLETE,NULL,$LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,$GOODADJ,$LASIZEADJ,$HIGHVALIMPMASQFTDIFF,
						  $MKTLEVELERDETAILADJ,NULL,$NETADJ,NULL,$INDICATEDVAL,$INDICATEDVALSQFT);	
	
$mafield = "Imprv_det_type_cd";
$allowablema = array("1/2","1ST","2ND","3RD","4TH","5TH","ADDL","ATRM","BELOW","CONC","DOWN","FBSMT","LOBBY","MEZZ","PBSMT","RSBLW","RSDN");

$landvaladjdelta ="LandValAdjDelta";
$classadjdelta = "ClassAdjDelta";
$goodadjdelta = "GoodAdjDelta";
$lasizeadjdelta = "LASizeAdjDelta";
$mktlevelerdetailadjdelta =	"MktLevelerDetailAdjDelta";
$segmentsadjdelta = "SegAdjDelta";

//TABLE NAMES
$TABLE_IMP_DET = "IMP_DET";
$TABLE_SPEC_IMP = "SPECIAL_IMP";
$TABLE_SALES = "SPECIAL_SALE_EX_CONF";
$TABLE_SALES_MERGED = "SALES_MLS_MERGED"; 
$TABLE_PROSPECT_LIST = "PROSPECT_LIST";
?>
