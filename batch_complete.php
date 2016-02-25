<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="index.css">
<title>Five Stone Property Tax TCAD Tools</title>
<style type="text/css"></style>
</head>
<body id="page2">
<img src="resources/FS-Logo-PropertyTax-wTagline.png" width="615" height="141" title="" alt="Five Stone Property Tax">
<br>
<br>
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
$resultAll=doSqlQuery($queryAll);
$numAll=mysqli_num_rows($resultAll);
mysqli_free_result($resultAll);

$query="SELECT * FROM BATCH_PROP WHERE completed='true'";
$result=doSqlQuery($query);
$num=mysqli_num_rows($result);
mysqli_free_result($result);

$queueQuery = "SELECT * FROM BATCH_PROP_SETTINGS WHERE id=(SELECT max(id) FROM BATCH_PROP_SETTINGS)";
$resultSettings = doSqlQuery($queueQuery);
$row = mysqli_fetch_array($resultSettings);
$TRIMINDICATED=$row['TrimIndicated'];
$MULTIHOOD=$row['MultiHood'];
$INCLUDEVU=$row['IncludeVU'];
$PREVYEAR=$row['NumPrevYears'];
$INCLUDEMLS=$row['IncludeMLS'];
$SQFTPCT=$row['SqftRange'];
$SUBCLASSRANGE=$row['ClassRange'];
$SUBCLASSENABLED=$row['ClassRangeEnabled'];
$PERCENTGOODRANGE=$row['PercentGood'];
$PERCENTGOODENABLED=$row['PercentGoodEnabled'];
$NET_ADJ_ENABLED=$row['NetAdjEnabled'];
$NET_ADJ_AMOUNT=$row['NetAdj'];
//REMEMBER adding something here means you also need to add to batch_pdf.php and updateBatchSettings.php
mysqli_free_result($resultSettings);

?>
<br>
<p><strong>Bulk Generation</strong>
<form action="upload_file.php" enctype="multipart/form-data" formtarget="_blank" method="post">
Choose a file to upload: <input name="file" type="file" /><br />
<p><input type="submit" name="submit" value="Submit" /></p>
</form>
<br>
<p><strong>Bulk Generation Settings</strong></p>
<form action="updateBatchSettings.php" formtarget="_blank">
    <input type="checkbox" name="trimindicated" id="trimindicated" value="yes" <?php echo ($TRIMINDICATED=='TRUE' ? 'checked' : '');?>>
    Only return properties with lower comparison values<br>
    <input type="checkbox" name="includemls" id="includemls" value="yes" <?php echo ($INCLUDEMLS=='TRUE' ? 'checked' : '');?>>
    Include MLS data.<br>
    <input type="checkbox" name="multihood" id="multihood" value="yes" <?php echo ($MULTIHOOD=='TRUE' ? 'checked' : '');?>>
    Include related neighborhoods<br>
    <input type="checkbox" name="includevu" id="includevu" value="yes" <?php echo ($INCLUDEVU=='TRUE' ? 'checked' : '');?>>
    Include forclosures (VU)<br>
    <input type="checkbox" name="subclassenabled" id="subclassenabled" value="yes" <?php echo ($SUBCLASSENABLED=='TRUE' ? 'checked' : '');?>>
    Range of subclasses to include:<input type="text" name="range" size="1" value=<?php echo $SUBCLASSRANGE;?>><br>
    <input type="checkbox" name="pctgoodenabled" id="pctgoodenabled" value="yes" <?php echo ($PERCENTGOODENABLED=='TRUE' ? 'checked' : '');?>>
    % Good Adjustment Range (amount above and below subject):<input type="text" name="pctGoodRange"  size="3" value=<?php echo $PERCENTGOODRANGE;?>>%<br>
    <input type="checkbox" name="netadjust" id="netadjust" value="yes " <?php echo($NET_ADJ_ENABLED=='TRUE' ? 'checked' : '');?>>
    Filter based on net adjustment value of <input type="text" name="netadjustamount"  size="7" value=<?php echo$NET_ADJ_AMOUNT;?>><br>
    Years back to include:<input type="text" name="multiyear" size="1" value=<?php echo $PREVYEAR;?>><br>
    Percent of square footage to consider (.01-1.00):<input type="text" name="sqftPct" size="3" value=<?php echo $SQFTPCT;?>><br>
    <br/>
    <input type="submit" value="Update Bulk Settings"/>
</form>
<h2><?php echo $numAll?> properties in batch queue</h2>
<h2><?php echo $num?> completed batch properties</h2>
<h2><?php echo $numAll - $num?> remaining to process</h2>
<p>
<a href='download_pdf.php?subj=ALL'>Download All Completed PDF Reports</a>
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
$rs_result = doSqlQuery($sql); 
$row = mysqli_fetch_row($rs_result); 
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
$rs_result = doSqlQuery($sql);
?> 
<table>
<tr><td>PropID (click for pdf)</td>
<td>Subj Market Val</td>
<td>Median (Sales 5)</td>
<td>Median (Sales 10)</td>
<td>Median (Sales 15)</td>
<td>Median (Equity 11)</td>
<td><form action="reset.php" method="get"><button type="submit" name="subj" value="ALL">Reset All</button></form></td>
</tr>
<?php 
while ($row = mysqli_fetch_assoc($rs_result)) { 
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
</body>
</html>