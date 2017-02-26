<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/24/16
 * Time: 11:10 AM
 */

include_once '../library/queryContext.php';

class queryContextTest extends PHPUnit_Framework_TestCase
{

    public function testParseQueryString_properties(){
        $_GET =[
                 'target'=>"properties",
                 'propid'=>"708686",
                 'c1'=>"729770",
                 'c1sp'=>"595000",
                 'c1sd'=>"9/29/2010",
                 'c2'=>"729775",
                 'c2sp'=>"605775",
                 'c2sd'=>"07/29/2010",
                 'c3'=>"783824",
                 'c3sp'=>"685000",
                 'c3sd'=>"12-15-2008",
                 'Submit'=>"Build Sales Table"
                     ];

        $queryContext = new queryContext();
        $queryContext->parseQueryString($_GET);

        $this->assertEquals(708686, $queryContext->subjPropId);
        $this->assertEquals(3, count($queryContext->compInfo));

        var_dump($queryContext);
    }

    public function testParseQueryString_propertiesNoSalesData(){
        $_GET =[
            'target'=>"properties",
            'propid'=>"708686",
            'c1'=>"729770",
            'c2'=>"729775",
            'c3'=>"783824"
        ];

        $queryContext = new queryContext();
        $queryContext->parseQueryString($_GET);

        $this->assertEquals(708686, $queryContext->subjPropId);
        $this->assertEquals(3, count($queryContext->compInfo));

        var_dump($queryContext);
    }

    public function testParseQueryString_propertiesSingleComp(){
        $_GET =[
            'target'=>"properties",
            'propid'=>"708686",
            'c1'=>"729770",
            'c1sp'=>"595000",
            'c1sd'=>"9/29/2010",
            'Submit'=>"Build Sales Table"
        ];

        $queryContext = new queryContext();
        $queryContext->parseQueryString($_GET);

        $this->assertEquals(708686, $queryContext->subjPropId);
        $this->assertEquals(1, count($queryContext->compInfo));

        var_dump($queryContext);
    }

    public function testParseQueryString_sqft(){
        $_GET = [
          'sqftPct'=>'10'
        ];

        $queryContext = new queryContext();
        $queryContext->parseQueryString($_GET);

        $this->assertEquals(10, $queryContext->sqftPercent);
        $this->assertEquals(null, $queryContext->sqftRangeMin);
        $this->assertEquals(null, $queryContext->sqftRangeMax);
        var_dump($queryContext);
    }

    public function testParseQueryString_sqftMinMax(){
        $_GET = [
            'sqftPct'=>'1000:3000'
        ];

        $queryContext = new queryContext();
        $queryContext->parseQueryString($_GET);

        $this->assertEquals(null, $queryContext->sqftPercent);
        $this->assertEquals(1000, $queryContext->sqftRangeMin);
        $this->assertEquals(3000, $queryContext->sqftRangeMax);
        var_dump($queryContext);
    }

    public function testParseQueryString_tcad(){
        $_GET = [
            'limitTcadScores'=>'off'
        ];
        $queryContext = new queryContext();
        $queryContext->parseQueryString($_GET);

        $this->assertEquals(false, $queryContext->limitTcadScores);

        $_GET2 = [
            'limitTcadScores'=>'on',
            'limitTcadScoresAmount'=>'50'
        ];

        $queryContext = new queryContext();
        $queryContext->parseQueryString($_GET2);

        $this->assertEquals(true, $queryContext->limitTcadScores);
        $this->assertEquals(50, $queryContext->limitTcadScoresAmount);
        $this->assertEquals(null, $queryContext->tcadScoreLimitMin);
        $this->assertEquals(null, $queryContext->tcadScoreLimitMax);
        var_dump($queryContext);
    }

    public function testParseQueryString_tcadMinMax(){
        $_GET = [
            'limitTcadScores'=>'on',
            'limitTcadScoresAmount'=>'90:95'
        ];

        $queryContext = new queryContext();
        $queryContext->parseQueryString($_GET);

        $this->assertEquals(true, $queryContext->limitTcadScores);
        $this->assertEquals(null, $queryContext->limitTcadScoresAmount);
        $this->assertEquals(90, $queryContext->tcadScoreLimitMin);
        $this->assertEquals(95, $queryContext->tcadScoreLimitMax);
        var_dump($queryContext);
    }

    public function testParseQueryString_pctGood(){
        $_GET = [
            'pctGoodRangeEnabled'=>'off'
        ];
        $queryContext = new queryContext();
        $queryContext->parseQueryString($_GET);

        $this->assertEquals(false, $queryContext->percentGoodRangeEnabled);

        $_GET2 = [
            'pctGoodRangeEnabled'=>'on',
            'pctGoodRange'=>'2'
        ];

        $queryContext = new queryContext();
        $queryContext->parseQueryString($_GET2);

        $this->assertEquals(true, $queryContext->percentGoodRangeEnabled);
        $this->assertEquals(2, $queryContext->percentGoodRange);
        $this->assertEquals(null, $queryContext->percentGoodMin);
        $this->assertEquals(null, $queryContext->percentGoodMax);
        var_dump($queryContext);

        $_GET3 = [
            'pctGoodRangeEnabled'=>'on',
            'pctGoodRange'=>'2:3'
        ];

        $queryContext = new queryContext();
        $queryContext->parseQueryString($_GET3);

        $this->assertEquals(true, $queryContext->percentGoodRangeEnabled);
        $this->assertEquals(null, $queryContext->percentGoodRange);
        $this->assertEquals(2, $queryContext->percentGoodMin);
        $this->assertEquals(3, $queryContext->percentGoodMax);
        var_dump($queryContext);
    }
}
