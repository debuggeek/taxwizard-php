<?php

include "library/functions.php";

$propid = 708686;
$queryContext = new queryContext();

$debug=true;
error_reporting(E_ALL);

print "Start Test";

$property = getSubjProperty($propid);

findBestCompsTest();

print "Finish Test";



function findBestCompsTest(){
    global $property,$queryContext;


    $compsarray = findBestComps($property,$queryContext);

    assert(sizeof($compsarray) == 10);
}