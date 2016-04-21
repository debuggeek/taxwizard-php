<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 6/9/14
 * Time: 6:12 PM
 */

session_start();
include 'library/propertyClass.php';
include 'library/functions.php';
include 'library/presentation.php';
$debug = false;
$COMPSTODISPLAY = 100;
$LIMIT=NULL;
$TRIMINDICATED = false;
$INCLUDEMLS = false;
$MULTIHOOD = false;
$INCLUDEVU = false;

global $INDICATEDVAL,$fieldsofinteresteq;

//Treat like equity so we use market val and not sales price
$isEquityComp = true;

$compInfo = array();

//Parse Inputs
$subjPropId = trim($_GET['s']);
if(isset($_GET['Submit'])){
    if($_GET['Submit'] == 'Build Sales Table'){
        $isEquityComp = false;
    }
}

$compInt = 1;
while(true){
    if(isset($_GET['c'.$compInt])){
        $id = trim($_GET['c'.$compInt]);
        if(isset($_GET['c'.$compInt.'sp'])){
            $saleprice = trim($_GET['c'.$compInt.'sp']);
        } else {
            $saleprice = null;
        }
        if(isset($_GET['c'.$compInt.'sd'])){
            $saledate = trim($_GET['c'.$compInt.'sd']);
        } else {
            $saledate = null;
        }
        $compInfo[] = array("id"=>$id,"salePrice"=>$saleprice,"saleDate"=>$saledate);
        $compInt++;
    } else {
        break;
    }
}

if(isset($_GET['exclude'])){
    $excludeStrList = trim($_GET['exclude']);
    $queryContext->excludes = explode('_',$excludeStrList);
}

if($subjPropId != "")
    $abort = false;

if($abort){
    echo "<p>Please enter a value</p>";
    exit;
}

$subjProperty = getSubjProperty($subjPropId);

error_log("Building subjcomparray for ".$subjPropId);
$subjcomparray = array();
$subjcomparray[0] = $subjProperty;

foreach($compInfo as $compIn){
    if(in_array($compIn['id'], $queryContext->excludes)){
        error_log("Removing ".$compIn['id']." from comp results due to being in excludes");
        continue;
    }
    $c = getProperty($compIn['id']);
    $c->setSalePrice($compIn['salePrice']);
    $c->mSaleDate = $compIn['saleDate'];
    calcDeltas($subjProperty,$c);
    $subjcomparray[] = $c;
}

$fullTable = array();
$fullTable["subjComps"] = $subjcomparray;

$fullTable["meanVal"] = getMeanVal($subjcomparray);
$fullTable["meanValSqft"] = getMeanValSqft($subjcomparray);
$fullTable["medianVal"]= getMedianVal($subjcomparray);
$fullTable["medianValSqft"] = getMedianValSqft($subjcomparray);

echo generateJsonRows($fullTable,$isEquityComp);
?>