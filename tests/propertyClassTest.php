<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/2/16
 * Time: 9:55 AM
 */
include_once '../library/propertyClass.php';
include_once "../library/ImpHelper.php";
include_once "../library/functions.php";

use PHPUnit\Framework\TestCase;

class propertyClassTest extends TestCase{

    /* @var propertyClass */
    public $property;

    protected function setUp()
    {
        global $debug;

        $debug = false;
        $this->property = getProperty(121352);
    }

    public function test_getImpCount(){
        $this->assertEquals(2, $this->property->getImpCount());
    }

    public function test_toJson(){
        $this->assertJson($this->property->toJson(), "Not valid JSON");
    }
    
    public function testIndicators(){
        $this->markTestSkipped('must be revisited.');
        //Current indicators are:
        //  "." after SalePrice if saleType is 'VQ'
        //  "_" after SaleDate if from MLS
        //  "_" after GoodAdj if >25 years old and GoodAdj > 75
    }
    
    public function testProperties(){
        $this->assertNotEmpty($this->property->getYearBuilt());
    }
}
