<?php
include_once("functions.php");

$query="SELECT * FROM PROSPECT_LIST WHERE market_val>0";
$result=doSqlQuery($query);

if (!$result) die('Couldn\'t fetch records');
$num_fields = mysqli_num_fields($result);
$num=mysqli_num_rows($result);
$headers = array();
for ($i = 0; $i < $num_fields; $i++) {
    $headers[] = mysqli_field_name($result , $i);
}
$fp = fopen('php://output', 'w');
if ($fp && $result) {
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="export.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    fputcsv($fp, $headers);
    while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {
        fputcsv($fp, $row);
    }
    die;
}
?>