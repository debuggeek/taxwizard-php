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
}
