<?php

include_once '../library/functions.php';
include_once '../library/queryContext.php';

class functionsTest extends PHPUnit_Framework_TestCase{

    public function test_getSubjProperty() {
        $propid = 224540;

        $property = getSubjProperty($propid);
        print $property;

        $this->assertTrue($property->isSubj());
        $this->assertEquals($propid, $property->getPropID());
        $this->assertEquals('0225020714', $property->mGeoID);
        $this->assertEquals('Y3000', $property->mNeighborhood);
        //$this->assertEquals('180', $property->getNMIA());
        //$this->assertEquals(550227, $property->mMarketVal);

    }

    public function test_findBestComps() {
        $propid = 105290;
        $queryContext = new queryContext();

        print "Start Test";

        $queryContext->netAdjustEnabled = true;
        $queryContext->netAdjustAmount = 10000;

        $debug=true;
        error_reporting(E_ALL);

        $property = getSubjProperty($propid);

        $compsarray = findBestComps($property,$queryContext);
        $this->assertGreaterThan(10, sizeof($compsarray));
    }

    public function test_2ImpIssue(){
        $subjId = 105290;
        $compId = 106033;

        $debug = true;
        $subjProperty = getSubjProperty($subjId);
        $compProperty = getProperty($compId);
        $compProperty->setSalePrice(599500);
        $compProperty->mSaleDate = '2/18/2014';
        calcDeltas($subjProperty, $compProperty);

        $this->assertEquals(18993, $compProperty->getClassAdjDelta());
        $this->assertEquals(-19975, $compProperty->getGoodAdjDelta());
        $this->assertEquals(17, count($compProperty->getImpDets()));

        $this->assertEquals(77032, $compProperty->getNetAdj());
    }


    public function test_GoodAdj(){
        $subjId = 224540;
        $compId = 224789;

        $debug = true;
        $subjProperty = getSubjProperty($subjId);
        $compProperty = getProperty($compId);
        $compProperty->setSalePrice(475000);
        $compProperty->mSaleDate = '2/18/2014';
        calcDeltas($subjProperty, $compProperty);

        $this->assertEquals(-58625, $compProperty->getLandValueAdjDelta());
        $this->assertEquals(5135, $compProperty->getClassAdjDelta());
        $this->assertEquals(14000, $compProperty->getGoodAdjDelta());
    }

    public function test_SampleProvided() {
        $subjId = 138282;
        $compId = 138972;
//        $queryContext = new queryContext();
//
//        $queryContext->netAdjustEnabled = true;
//        $queryContext->netAdjustAmount = 10000;

        $debug=true;
        $subjProperty = getSubjProperty($subjId);
        $compProperty = getProperty($compId);
        $compProperty->setSalePrice(599500);
        $compProperty->mSaleDate = '2/18/2014';
        calcDeltas($subjProperty, $compProperty);

        $this->assertEquals(713518, $subjProperty->mMarketVal);
        $this->assertEquals(3166, $subjProperty->getLivingArea());

        $this->assertEquals(764506, $compProperty->mMarketVal);
        $this->assertEquals(2839, $compProperty->getLivingArea());

        $this->assertEquals($compProperty->mNeighborhood, $subjProperty->mNeighborhood);

        $this->assertEquals(42000,$compProperty->mLandValAdjDelta);
        $this->assertEquals(18993, $compProperty->getClassAdjDelta());
        $this->assertEquals(-19975, $compProperty->mGoodAdjDelta);
    }

    public function test_getHoodList(){
        $subjId = 105290;
        $queryContext = new queryContext();

        $subjProp = getSubjProperty($subjId);
        $hoodList = getHoodList($subjProp->mNeighborhood, $queryContext);
        $this->assertEquals(400, count($hoodList));
    }

    public function test_addToCompsArray_ImpLimit(){
        $subjId = 100120;
        $compId = 302891;
        $compId2 = 464549;
        $queryContext = new queryContext();
        $queryContext->limitToLessImps = true;

        $subjProperty = getSubjProperty($subjId);
        $compProperty = getProperty($compId);
        $compProperty2 = getProperty($compId2);

        $this->assertFalse(addToCompsArray($compProperty, $subjProperty, $queryContext));
        $this->assertTrue(addToCompsArray($compProperty2, $subjProperty, $queryContext));
    }
}