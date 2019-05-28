<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 6/25/16
 * Time: 11:10 PM
 */

include_once '../library/ImprovementDetailClass.php';

use PHPUnit\Framework\TestCase;

class ImprovementDetailClassTest extends TestCase
{

    public function test_getAdjustedPerc(){
        $impDetail = new ImprovementDetailClass();
        $impDetail->setAdjPercRaw('S00966: 0.00%; L2000: 125.00%');

        $this->assertEquals(1.25, $impDetail->getAdjustedPerc());
    }

    public function test_getAdjustedPerc_threeAdjustments(){
        $impDetail = new ImprovementDetailClass();
        $impDetail->setAdjPercRaw('S16089: 100.00%; J2600: 201.00%; MKT: 90.00%');

        $this->assertEquals(2.01, $impDetail->getAdjustedPerc());
    }
}
