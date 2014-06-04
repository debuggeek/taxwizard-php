<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="index.css">
<title>Tax Tiger TCAD Tools</title>
<style type="text/css"></style>
</head>
<body id="page2">
<img src="fivestonetax-logo.png" width="615" height="141" title="" alt="">
<ul id="tabs">
<li id="tab1"><a href="index.html">Table Lookups</a></li>
<li id="tab2"><a href="batch_complete.php">Batch Processing</a></li>
<li id="tab3"><a href="beta.html">Beta Tools</a></li>
<li id="tab4"><a href="admin.php">Admin Tools</a></li>
</ul>
<link rel="stylesheet" type="text/css" href="table.css">
<?php
include_once("library/functions.php");
$queryAll="SELECT * FROM BATCH_PROP";
$resultAll=executeQuery($queryAll);
$numAll=mysql_numrows($resultAll);


$query="SELECT * FROM BATCH_PROP WHERE completed='true'";
$result=executeQuery($query);
$num=mysql_numrows($result);
?>
<br>
<p><strong>Bulk Generation</strong>
<form action="upload_file.php" enctype="multipart/form-data" formtarget="_blank" method="post">
Choose a file to upload: <input name="file" type="file" /><br />
<p><input type="submit" name="submit" value="Submit" /></p>
</form>
<br>
<h2><?php echo $numAll?> properties in batch queue</h2>
<h2><?php echo $num?> completed batch properties</h2>
<h2><?php echo $numAll - $num?> remaining to process</h2>
<p>
<a href='library/download_pdf.php?subj=ALL'>Download All Completed PDF Reports</a>
<br>
<a href='library/download_csv.php?subj=ALL'>Download Simple Report CSV</a>
<br>
<form>
<input name="subj">
<input type="submit" value="Download Select PropID" formaction="download_pdf.php"/><br>
</form>

<br>
<?php 
$sql = "SELECT COUNT(prop) FROM BATCH_PROP WHERE completed='true'"; 
$rs_result = executeQuery($sql); 
$row = mysql_fetch_row($rs_result); 
$total_records = $row[0]; 
$total_pages = ceil($total_records / 20); 

echo "Page:";
for ($i=1; $i<=$total_pages; $i++) { 
            echo "<a href='batch_complete.php?page=".$i."'>".$i."</a> "; 
}; 
?>
<?php 
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
$start_from = ($page-1) * 20; 
$sql = "SELECT * FROM BATCH_PROP WHERE completed='true' ORDER BY prop ASC LIMIT $start_from, 20"; 
$rs_result = executeQuery($sql);
?> 
<table>
<tr><td>PropID (click for pdf)</td>
<td>Subj Market Val</td>
<td>Median (Sales 5)</td>
<td>Median (Sales 10)</td>
<td>Median (Sales 15)</td>
<td>Median (Equity 11)</td>
<td><a href='reset.php?subj=ALL'>Reset All</a></td>
</tr>
<?php 
while ($row = mysql_fetch_assoc($rs_result)) { 
?> 
            <tr>
            <td><? echo "<a href='download_pdf.php?subj=".$row["prop"]."'>".$row["prop"]."</a>"; ?></td>
            <td><? echo $row["prop_mktval"]; ?></td>
            <td><? echo $row["Median_Sale5"]; ?></td>
            <td><? echo $row["Median_Sale10"]; ?></td>
            <td><? echo $row["Median_Sale15"]; ?></td>
            <td><? echo $row["Median_Eq11"]; ?></td>
            <td><? echo "<a href='reset.php?subj=".$row["prop"]."'>recompute</a>"; ?></td>
            </tr>
<?php 
}; 
?> 
</table>
<!-- 
table>
?php
$i=0;

while ($i < $num) {
	$rowArray = array();
	if($i < $num-10){
		while($i % 10 != 0)
		{		
			$rowArray[]=mysql_result($result,$i,"prop");
			$i++;
		}
	} else {
		while($i < $num)
		{
			$rowArray[]=mysql_result($result,$i,"prop");
			$i++;
		}
	}

?>
<tr>
?php 
	foreach($rowArray as $propid){
?>
<td>?php echo "<a href='download_pdf.php?subj=".$propid."'>$propid</a>"; ?></td>
	?php
	}
	?>
</tr>
?php
}
?>
</table>
 -->
</body>
</html>