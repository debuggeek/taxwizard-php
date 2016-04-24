<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="index.css">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Five Stone Property Tax TCAD Tools</title>
 <?php include_once("library/accountinfo.php");?>
</head>
<body id="page4">
<img src="resources/FS-Logo-PropertyTax-wTagline.png" width="615" height="141" title="" alt="Five Stone Property Tax">
<br>
<br>
<ul id="tabs">
<li id="tab1"><a href="index.html">Table Lookups</a></li>
<li id="tab2"><a href="batch_complete.php">Batch Processing</a></li>
<li id="tab3"><a href="beta.html">Beta Tools</a></li>
<li id="tab4"><a href="admin.php">Admin Tools</a></li></ul>
Database:<?php echo $database ?>
<p><strong>Test Samples</strong><br>
<a href="resultSubjComps.html?target=properties&propid=708686&amp;c1=729770&amp;c1sp=595000&amp;c1sd=9%2F29%2F2010&amp;c2=729775&amp;c2sp=605775&amp;c2sd=07%2F29%2F2010&amp;c3=783824&amp;c3sp=685000&amp;c3sd=12-15-2008&amp;Submit=Build+Sales+Table">Sales Comp Sample</a><br>
<a href="resultSubjComps.html?target=properties&propid=253870&amp;c1=253877&amp;c2=253882&amp;c3=253875">Equity Comp Sample</a><br>
<!--<a href="letter.php?hood=X9000&amp;ampSubmit=Search"> Neighborhood Sample</a><br>-->
<a href="bestcomps.php?propid=129972&amp;display=100&amp;Submit=Search">Find Comps</a><br>
<a href="bestcomps.php?propid=129972&amp;display=100&amp;Submit=Search&amp;equity=false">Find Comps Sales</a><br>
<a href="resultSubjComps.html?target=massreport&propid=129972&amp;display=10&amp;Submit=Search">Find Comps & Gen Report</a><br>
<a href="resultSubjComps.html?target=massreport&propid=129972&amp;display=10&amp;Submit=Search&amp;style=sales">Find Comps Sales & Gen Report</a><br>
<a href="resultSubjComps.html?target=massreport&propid=708686&amp;display=10&amp;Submit=Search&amp;style=sales">Test Empty Sales Comps</a><br>
<a href="resultSubjComps.html?target=properties&propid=100218&c1=101636&c1sp=450000&c1sd=02%2F01%2F2010&Submit=Build+Sales+Table">Test Comps with multi Improvements</a><br>
<a href="comps_pdf.php?subj=708686&complist=438058">PDF Test of comps_pdf</a><br>
</p>
<p>Prior Issues</p>
<a href="resultSubjComps.html?target=massreport&multiyear=1&limit=20&style=sales&propid=114667&S%26G=Search+and+Generate&sqftPct=2">Issue 17: Should return 0 hits</a><br>
<a href="resultSubjComps.html?target=massreport&multiyear=1&limit=20&style=sales&propid=114667&S%26G=Search+and+Generate&sqftPct=75">Issue 17: Should return many hits</a><br>
<a href="bestcomps.php?propid=141826&amp;display=100&amp;Submit=Search&amp;equity=false">Issue 14: Had Duplicate Comps</a><br>
<a href="resultSubjComps.html?target=massreport&multiyear=1&limit=10&style=sales&propid=718039&S%26G=Search+and+Generate">Issue 11:Duplicate between MLS and Sales</a><br>
<a href="resultSubjComps.html?target=massreport&includemls=off&multihood=off&limit=10&style=sales&propid=210850&S%26G=Search+and+Generate">Issue 8: Duplicate Comps</a><br>
<a href="resultSubjComps.html?target=properties&propid=105290&c1=104522&c1sp=739000&c1sd=2%2F18%2F2014&Submit=Build+Sales+Table">Issue 23: Multi Improvements</a>
<br>
<p><strong>Prospect information</strong>
<?php 
include_once("library/functions.php");

$query="SELECT COUNT(prop_id) FROM PROSPECT_LIST";
$result=doSqlQuery($query);
$resultCount=mysqli_result($result,0,"COUNT(prop_id)");
echo $resultCount." Prospects Found"
?>
<form name="form" action="prospects.php" method="get" target="_blank">
<input type="submit" name="prospects" value="Generate" <?php if($resultCount>0) echo 'disabled=""'?>>Mine for Prospects (Only need to run once per db)
<br>
<input type="submit" name="prospects" value="Lookup">LookupProspect<br>
<input type="text" name="propid"><input type="submit" name="prospects" value="Singleton">Test Singleton<br>
<a href="library/prospects_list.php">Show Prospect List</a>
</form>
<p><strong>Batch Admin Controls</strong>
</body>
</html>