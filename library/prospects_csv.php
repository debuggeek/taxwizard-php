<?php
include_once("functions.php");

$query="SELECT * FROM PROSPECT_LIST WHERE market_val>0";
$result=executeQuery($query);

if (!$result) die('Couldn\'t fetch records');
$num_fields = mysql_num_fields($result);
$num=mysql_numrows($result);
$headers = array();
for ($i = 0; $i < $num_fields; $i++) {
    $headers[] = mysql_field_name($result , $i);
}
$fp = fopen('php://output', 'w');
if ($fp && $result) {
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="export.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    fputcsv($fp, $headers);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
        fputcsv($fp, $row);
    }
    die;
}
?>