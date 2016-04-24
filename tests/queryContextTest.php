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
}
