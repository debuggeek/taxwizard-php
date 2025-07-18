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
    <li id="tab4"><a href="admin.php">Admin Tools</a></li>
</ul>
<link rel="stylesheet" type="text/css" href="table.css">
<?php
include_once("library/functions.php");
include_once("services/BatchService.php");

$batchService = new \DebugGeek\TaxWizard\Services\BatchService();

if (!empty($_POST)) {
    try {
        $batchService->insertJob($_POST["insertProp"]);
    } catch (PDOException $e){
        error_log($e);
    }
}

$numComplete = $batchService->getCompletedJobCount();
$numPending = $batchService->getPendingJobCount();

$numAll = $numComplete + $numPending;

$queryContext = $batchService->getBatchSettings();

?>
<br>
<p><strong>Bulk Generation</strong>
    <form action="upload_file.php" enctype="multipart/form-data" formtarget="_blank" method="post">
        Choose a file to upload: <input name="file" type="file" /><br />
<p><input type="submit" name="submit" value="Submit" /></p>
</form>
<form method="post">
    Insert single property into batch:
    <input name="insertProp" type="text">
    <input type="submit"/>
</form>
<br>
<p><strong>Bulk Generation Settings</strong></p>
<form action="updateBatchSettings.php" formtarget="_blank">
    <input type="checkbox" name="trimindicated" id="trimindicated"   <?php echo ($queryContext->trimIndicated =='TRUE' ? 'checked' : '');?>>
    Only return properties with lower comparison values<br>
    <input type="checkbox" name="includemls" id="includemls"   <?php echo ($queryContext->includeMls =='TRUE' ? 'checked' : '');?>>
    Include MLS data.<br>
    <input type="checkbox" name="multihood" id="multihood"   <?php echo ($queryContext->multiHood =='TRUE' ? 'checked' : '');?>>
    Include related neighborhoods<br>
    <input type="checkbox" name="includevu" id="includevu"   <?php echo ($queryContext->includeVu =='TRUE' ? 'checked' : '');?>>
    Include forclosures (VU)<br>
    <input type="checkbox" name="limitImps" <?php echo ($queryContext->limitToLessImps =='TRUE' ? 'checked' : '');?>>
    Limit comps to properties with fewer Improvements<br>
    <input type="checkbox" name="rangeEnabled" id="rangeEnabled"   <?php echo ($queryContext->subClassRangeEnabled =='TRUE' ? 'checked' : '');?>>
    Range of subclasses to include:<input type="text" name="range" size="1" value=<?php echo $queryContext->subClassRange;?>><br>
    <input type="checkbox" name="pctGoodRangeEnabled" id="pctGoodRangeEnabled"   <?php echo ($queryContext->percentGoodRangeEnabled =='TRUE' ? 'checked' : '');?>>
    % Good Adjustment Range (amount above and below subject):<input type="text" name="pctGoodRange"  size="3" value=<?php echo $queryContext->percentGoodRange;?>>%<br>
    <input type="checkbox" name="netadjust" id="netadjust"   <?php echo($queryContext->netAdjustEnabled=='TRUE' ? 'checked' : '');?>>
    Filter based on net adjustment value of <input type="text" name="netadjustamount"  size="7" value=<?php echo$queryContext->netAdjustAmount;?>><br>
    Years back to include:<input type="text" name="multiyear" size="1" value=<?php echo $queryContext->prevYear;?>><br>
    Percent of square footage to consider:<input type="text" name="sqftPct" size="3" value=<?php echo $queryContext->sqftPercent;?>>%<br>
    <br/>
    <input type="submit" value="Update Bulk Settings"/>
</form>
<h2><?php echo $numAll?> properties submitted for batch processing</h2>
<h2><?php echo $numComplete?> completed batch properties</h2>
<h2><?php echo $numPending?> remaining to process</h2>
<p>
    <a href='download_pdf.php?subj=ALL'>Download All Completed PDF Reports into zip file</a>
    <br>
    <a href='library/download_csv.php?subj=ALL'>Download Simple Report CSV</a>
    <br>
<form>
    <input name="subj">
    <input type="submit" value="Download Select PropID" formaction="download_pdf.php"/><br>
</form>

<br>
<?php
$total_pages = ceil($numComplete / 20);
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$start_from = ($page-1) * 20;

echo "$total_pages Page(s):";
if($page != 1){
    echo "<a href='batch_complete.php?page=1'> << First </a>";
    echo "<a href='batch_complete.php?page=". ($page-1)."'>  Prev  </a>";
}
for ($i=$page; $i<=min($page+9,$total_pages); $i++) {
    echo "<a href='batch_complete.php?page=".$i."'>".$i."</a> ";
};
echo "<a href='batch_complete.php?page=". ($page + 1) ."'>  Next  </a>";
echo "<a href='batch_complete.php?page=$total_pages'><nbsp>Last>></a>";
?>
<?php
$batchJobs = $batchService->getPagedBatchJobs(true, $start_from, 20);
?>
<table>
    <tr><td>PropID (click for pdf)</td>
        <td>Subj Market Val</td>
        <td>Median (Sales 5)</td>
        <td>Median (Sales 10)</td>
        <td>Median (Sales 15)</td>
        <td>Median (Equity 11)</td>
        <td>Total Sales Comps Found</td>
        <td><form action="reset.php" method="get"><button type="submit" name="subj" value="ALL">Reset All</button></form></td>
        <td><form action="remove.php" method="get"><button type="submit" name="propid" value="ALL">Remove All</button></form></td>
    </tr>
    <?php
    foreach ($batchJobs as $row) {
        ?>
        <tr>
            <td><?php echo "<a href='download_pdf.php?subj=".$row->propId."'>".$row->propId."</a>"; ?></td>
            <td><?php echo number_format($row->propMktVal); ?></td>
            <td><?php echo number_format($row->propMedSale5); ?></td>
            <td><?php echo number_format($row->propMedSale10); ?></td>
            <td><?php echo number_format($row->propMedSale15); ?></td>
            <td><?php echo number_format($row->propMedEq11); ?></td>
            <td><?php echo number_format($row->totalSalesComps); ?></td>
            <td><?php echo "<a href='reset.php?subj=".$row->propId."'>recompute</a>"; ?></td>
            <td><?php echo "<a href='remove.php?propid=".$row->propId."'>remove</a>"; ?></td>
        </tr>
        <?php
    };
    ?>
</table>
</body>
</html>