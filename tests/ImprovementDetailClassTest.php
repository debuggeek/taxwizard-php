<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 6/25/16
 * Time: 11:10 PM
 */

include_once '../library/ImprovementDetailClass.php';

class ImprovementDetailClassTest extends PHPUnit_Framework_TestCase
{

    public function test_getAdjustedPerc(){
        $impDetail = new ImprovementDetailClass();
        $impDetail->setAdjPercRaw('S00966: 0.00%; L2000: 125.00%');

        $this->assertEquals(1.25, $impDetail->getAdjustedPerc());
    }
}
