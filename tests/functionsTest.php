<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 7/1/15
 * Time: 9:19 PM
 */
include "library/functions.php";

$propid = 708686;
$isEquityComp = false;
$SQFTPERCENT = 1.0;
$TRIMINDICATED = false;
$MULTIHOOD = false;
$INCLUDEVU = false;
$PREVYEAR = 1;
$SUBCLASSRANGE = 3;

$debug=true;
error_reporting(E_ALL);

print "Start Test";

$property = getSubjProperty($propid);

findBestCompsTest();

print "Finish Test";



function findBestCompsTest(){
    global $property,$isEquityComp,$SQFTPERCENT,$TRIMINDICATED,$MULTIHOOD,$INCLUDEVU,$PREVYEAR,$SUBCLASSRANGE;
    $compsarray = findBestComps($property,$isEquityComp,$SQFTPERCENT,$TRIMINDICATED,$MULTIHOOD,$INCLUDEVU,$PREVYEAR,$SUBCLASSRANGE);

    assert(sizeof($compsarray) == 5);
}