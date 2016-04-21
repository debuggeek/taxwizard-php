<?php
include_once 'library/propertyClass.php';
include_once 'library/functions.php';
include_once 'library/presentation.php';

$debug = false;

$queryContext = new queryContext();

if(isset($_GET['propid'])){
    $queryContext->subjPropId = trim($_GET['propid']);
}

if($queryContext->subjPropId == ""){
    echo json_encode(array("error" => "Must provide subject property id", "propId"=>null));
    exit;
}

$property = getSubjProperty($queryContext->subjPropId);
error_log("Building subjcomparray for ".$queryContext->subjPropId);
$subjcomparray = array();
$subjcomparray[0] = $property;

$fullTable = array();
$fullTable["subjComps"] = $subjcomparray;

$fullTable["meanVal"] = getMeanVal($subjcomparray);
$fullTable["meanValSqft"] = getMeanValSqft($subjcomparray);
$fullTable["medianVal"]= getMedianVal($subjcomparray);
$fullTable["medianValSqft"] = getMedianValSqft($subjcomparray);

echo generateJsonRows($fullTable);