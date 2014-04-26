<?php
//GLOBALS
include 'accountinfo.php';
$debug = false;

$c = count($_GET);
$keys = array_keys($_GET);
$comparr = array();
$comparrsp = array();
$comparrsd = array();
$isEquityComp = false;

$table="PROP";
$data = array();
$curryear = 2009;

$PROPID = array("Property ID","prop","prop_id");
$GEOID = array("Geo ID","prop","geo_id");
$SITUS = array("Situs","prop",array("situs_num","situs_street_prefx","situs_street","situs_street_suffix","-TX","situs_zip"));
$NEIGHB = array("Neighborhood","prop","hood_cd");
//$NEIGHBMIA = array("Neighborhood Mass Improv Adj","TABLELOOKUP","adjust_perc","TABLE"=>"special_imp");
$NEIGHBMIA = array("Neighborhood Mass Improv Adj","CALCULATED","getNMIA","FIELD"=>"adjust_perc","TABLE"=>"SPECIAL_IMP");

$MARKETVALUE = array("Market Value","prop","market_value");
$MARKETPRICESQFT = array("Market Price/SQFT","CALCULATED","getMrktSqft");
$LIVINGAREA = array("Living Area","TABLELOOKUP","liv_area","TABLE"=>"SPECIAL_PROPDATA");

$SALEDATE = array("Sale Date","TABLELOOKUP","sale_date","TABLE"=>"SPECIAL_SALE_EX_CONF");
$SALEPRICE = array("Sale Price","TABLELOOKUP","sale_price","TABLE"=>"SPECIAL_SALE_EX_CONF");
$SALEPRICESQFT = array("Sale Price / SQFT","CALCULATED","getSPSqft");

$IMPROVEMENTCNT = array("Improvment Count","-CONST","-1");
$HIGHVALIMPMARCN = array("High Value Improv MA RCN","TABLELOOKUP","det_calc_val","TABLE"=>"SPECIAL_IMP");
$HIGHVALIMPMARCNSQFT = array("High Value Improv MA RCN/SQFT","CALCULATED","getHVImpSqft");
$COMPLETE = array("% Complete","-CONST","-100");

$LANDVALUEADJ =	array("Land Value Adj","prop","land_hstd_val");
$LANDVALUEADJB =	array("Land Value Adj","PROP","land_non_hstd_val");
$CLASSADJ = array("Class Adj","CALCULATED","getClassAdj");
$ACTUALYEARBUILT = array("Actual Year Built","TABLELOOKUP","yr_built","TABLE"=>"IMP_DET");
$GOODADJ = array("% Good Adjustment","CALCULATED","getGoodAdj","TABLE"=>"SPECIAL_IMP","FIELD"=>"det_base_deprec_perc");
$LASIZEADJ = array("L/A Size Adj","CALCULATED","getLASizeAdj");
$HIGHVALIMPMASQFTDIFF = array("High Value Improv MA SQFT Diff","CALCULATED","getHVImpSqftDiff");
$MKTLEVELERDETAILADJ = array("Mkt Leveler Detail Adj","TABLELOOKUP","det_calc_val","TABLE"=>"SPECIAL_IMP");
$SEGMENTSADJ = array("Segments & Adj","-CONST","-0");
$NEIGHBADJ = array("Neighborhood Adj","CALCULATED","getNeigbAdj");

$NETADJ = array("Net Adjustment","CALCULATED","getNetAdj");

$INDICATEDVAL = array("Indicated Value","CALCULATED","getIndicatedVal");
$INDICATEDVALSQFT = array("Indicated Value / SQFT","CALCULATED","getIndicatedValSqft");


$MEANVAL = array("Mean Value","CALCULATED","getMeanVal");
$MEANVALSQFT = array("Mean Value / SQFT","CALCULATED","getMeanValSqft");
$MEDIANVAL = array("Median Value","CALCULATED","getMedianVal");
$MEDIANVALSQFT = array("Median Value / SQFT", "CALCULATED","getMedianValSqft");

						  
$fieldsofinterest = array($PROPID,$GEOID,$SITUS,NULL,$MARKETVALUE,$MARKETPRICESQFT,$LIVINGAREA,NULL,
						  $SALEDATE,$SALEPRICE,$SALEPRICESQFT,NULL,$IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,
						  $COMPLETE,NULL,$LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,$GOODADJ,$LASIZEADJ,$HIGHVALIMPMASQFTDIFF,
						  $MKTLEVELERDETAILADJ,NULL,$NETADJ,NULL,$INDICATEDVAL,$INDICATEDVALSQFT,NULL,$MEANVAL,
						  $MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT);	
						  
$fieldsofinteresteq = array($PROPID,$GEOID,$SITUS,$NEIGHB,$NEIGHBMIA,NULL,$MARKETVALUE,$MARKETPRICESQFT,$LIVINGAREA,NULL,
						  $IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,
						  $COMPLETE,NULL,$LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,$GOODADJ,$LASIZEADJ,$HIGHVALIMPMASQFTDIFF,
						  $MKTLEVELERDETAILADJ,NULL,$NETADJ,NULL,$INDICATEDVAL,$INDICATEDVALSQFT,NULL,$MEANVAL,
						  $MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT);	
	
$mafield = "Imprv_det_type_cd";
$allowablema = array("1/2","1ST","2ND","3RD","4TH","5TH","ADDL","ATRM","BELOW","CONC","DOWN","FBSMT","LOBBY","MEZZ","PBSMT","RSBLW","RSDN");

$goodadjdelta = "getGoodAdjDelta";
$landvaladjdelta = "getLandValAdjDelta";
$classadjdeltafunc = "getClassAdjDelta";
$goodadjdeltafunc = "getGoodAdjDelta";
$lasizeadjdelta = "getLASizeAdjDelta";
$segadjdeltafunc = "getSegAdjDelta";
$mktlevelerdetailadjdelta =	"getMktLevelerDetailAdjDelta";
$neighbadjdelta = "getNeigbAdjDelta";

$UNITPRICE = array("Unit Price","TABLE" => "SPECIAL_IMP","FIELD"=>"det_unitprice");
?>


