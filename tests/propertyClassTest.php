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

class propertyClassTest extends PHPUnit_Framework_TestCase{

    /* @var propertyClass */
    public $property;

    protected function setUp()
    {
        global $debug;

        $debug = false;
        $this->property = getProperty(105290);
    }

    public function test_getImpCount(){
        $this->assertEquals(2, $this->property->getImpCount());
    }

    public function test_toJson(){
        echo $this->property->toJson();
    }
    
    public function testIndicators(){
        //Current indicators are:
        //  "." after SalePrice if saleType is 'VQ'
        //  "_" after SaleDate if from MLS
        //  "_" after GoodAdj if >25 years old and GoodAdj > 75
        
    }
    
    public function testProperties(){
        $this->assertNotEmpty($this->property->getYearBuilt());
    }
}
