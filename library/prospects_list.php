<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../index.css">
<title>Tax Tiger TCAD Tools</title>
<style type="text/css"></style>
</head>
<body id="prospects">
<img src="../TaxTTitle.png" width="623" height="99" title="" alt="">
<ul id="tabs">
<li id="tab1"><a href="../index.html">Table Lookups</a></li>
<li id="tab2"><a href="prospects.html">Prospects</a></li>
<li id="tab3"><a href="../beta.html">Beta Tools</a></li>
<li id="tab4"><a href="admin.html">Admin Tools</a></li>
</ul>
<link rel="stylesheet" type="text/css" href="../table.css">
<?php
include_once("functions.php");

$query="SELECT * FROM PROSPECT_LIST WHERE market_val>0";
$result=doSqlQuery($query);
$num=mysqli_num_rows($result);
?>
<br>
<h2>Found <?php echo $num?> potential prospects</h2>
Showing first 100
<p>
<form>
<input type="submit" value="Export All to CSV" formaction="prospects_csv.php"/><br>
</form>
<br>
<table>
<tr><h1>
<td>test</td>
<td>Property ID</td><td>Property Owner</td><td>Address</td><td>Date/Time Computed</td><td>Market Value</td><td>Average Comp Value</td><td>Difference</td><td>Comp List</td>
</h1></tr>
<?php
$i=0;
while ($i < Min($num,100)) {
	$f1=mysqli_result($result,$i,"prop_id");
	$f2=mysqli_result($result,$i,"prop_owner");
	$f3=mysqli_result($result,$i,"prop_addr");
	$f4=mysqli_result($result,$i,"computed_date");
	$f5=mysqli_result($result,$i,"market_val");
	$f6=mysqli_result($result,$i,"mean_val");
	$f7=mysqli_result($result,$i,"diff");
	$f8=mysqli_result($result,$i,"comps_csv");
?>
<tr>
<td><?php echo "<a href='comps_pdf.php?subj=".$f1."&complist=".$f8."'>PDF</a>"; ?></td>
<td><?php echo "<a href='massreport.php?includemls=off&trimindicated=on&style=sales&propid=".$f1."&S%26G=Search+and+Generate'>". $f1."</a>"; ?></td>
<td><?php echo $f2; ?></td><td><?php echo $f3; ?></td><td><?php echo $f4; ?></td><td><?php echo $f5; ?></td><td><?php echo $f6; ?></td><td><?php echo number_format($f7*100,1); ?>%</td><td><?php echo $f8; ?></td>
</tr>
<?php
	$i++;
}
?>
</table>
</body>
</html>