<?php
include_once("functions.php");
$query = "SELECT prop,prop_mktval,Median_Sale5,Median_Sale10,Median_Eq11 FROM BATCH_PROP WHERE completed='true'";

error_reporting(0);

header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=download.csv");
header("Pragma: no-cache");
header("Expires: 0");

$export = doSqlQuery($query );
$fields = mysqli_num_fields( $export );
while( $row = mysqli_fetch_row( $export ) ){
    $line = '';
    foreach( $row as $value ){
        if ( ( !isset( $value ) ) || ( $value == "" ) ){
            $value = ",";
        }
        else{
            $value = str_replace( '"' , '""' , $value );
            $value = '"' . $value . '"' . ",";
        }
        $line .= $value;
    }
    $data .= trim( $line ) . "\n";

}
$data = str_replace( "Array" , "" , $data );
$data = str_replace( "\r" , "" , $data );

if ( $data == "" ){
    $data = "\n(0) Records Found!\n";
}
echo "$data";
exit();
?>