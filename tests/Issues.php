<?php

include_once '../library/functions.php';
class Issues extends PHPUnit_Framework_TestCase
{
    
    /*
     * Issue X - 2 IMP Issue
     */
    public function test_twoImpIssue() {
        $subjId = 105290;
        $compId = 106033;
//        $queryContext = new queryContext();
//
//        $queryContext->netAdjustEnabled = true;
//        $queryContext->netAdjustAmount = 10000;

        $debug=true;
        $subjProperty = getSubjProperty($subjId);
        $compProperty = getProperty($compId);
        $compProperty->mSalePrice = 599500;
        $compProperty->mSaleDate = '2/18/2014';
        calcDeltas($subjProperty, $compProperty);

        $this->assertEquals(720623, $subjProperty->mMarketVal);
        $this->assertEquals(2934, $subjProperty->mLivingArea);
        $this->assertEquals(13, $subjProperty->getImpDetCount());

        $this->assertEquals(628505, $compProperty->mMarketVal);
        $this->assertEquals(2839, $compProperty->mLivingArea);
        $this->assertEquals(12, $compProperty->getImpDetCount());

        $this->assertEquals($compProperty->mNeighborhood, $subjProperty->mNeighborhood);

        $this->assertEquals(42000,$compProperty->mLandValAdjDelta);
        $this->assertEquals(18993, $compProperty->mClassAdjDelta);
        $this->assertEquals(-19975, $compProperty->mGoodAdjDelta);
        $this->assertEquals(81550, $compProperty->getNetAdj());
        $this->assertEquals(681050, $compProperty->getIndicatedVal());

    }

    public function test_twoFloorIssue() {
        $subjId = 101455;
        $compId = 304393;
//        $queryContext = new queryContext();
//
//        $queryContext->netAdjustEnabled = true;
//        $queryContext->netAdjustAmount = 10000;

        $debug=true;
        $subjProperty = getSubjProperty($subjId);
        $compProperty = getProperty($compId);
        $compProperty->mSalePrice = 375000;
        $compProperty->mSaleDate = '2/18/2014';
        calcDeltas($subjProperty, $compProperty);

        $this->assertEquals(493809, $subjProperty->mMarketVal);
        $this->assertEquals(3117, $subjProperty->mLivingArea);
        $this->assertEquals(9, $subjProperty->getImpDetCount());

        $this->assertEquals(358312, $compProperty->mMarketVal);
        $this->assertEquals(1819, $compProperty->mLivingArea);
        $this->assertEquals(10, $compProperty->getImpDetCount());

        $this->assertEquals($compProperty->mNeighborhood, $subjProperty->mNeighborhood);

        $this->assertEquals(42000,$compProperty->mLandValAdjDelta);
        $this->assertEquals(18993, $compProperty->mClassAdjDelta);
        $this->assertEquals(-19975, $compProperty->mGoodAdjDelta);
        $this->assertEquals(81550, $compProperty->getNetAdj());
        $this->assertEquals(681050, $compProperty->getIndicatedVal());

    }

}
