<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/2/16
 * Time: 7:06 PM
 */

include_once "../library/PropertyDAO.php";
include_once "../library/ImpHelper.php";
include_once "../library/ImprovementDetailClass.php";

class ImpHelperTest extends PHPUnit_Framework_TestCase{

    var $HOST = 'localhost';
    var $user = "root";
    var $pw = "root";
    var $db = "TCAD_2015";

    public function test_compareImpDetails_AddDelta(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $subjImpArray = $propDao->getImpDet(105290);
        $compImpArray = $propDao->getImpDet(106033);
        $this->assertNotEmpty($subjImpArray);
        $this->assertNotEmpty($compImpArray);
        $this->assertEquals(13, count($subjImpArray));
        $this->assertEquals(12, count($compImpArray));

        $compImpArray = ImpHelper::compareImpDetails_AddDelta($subjImpArray, $compImpArray);
        $this->assertEquals(13, count($subjImpArray));
        $this->assertEquals(18, count($compImpArray));

        var_dump($compImpArray);
    }

    public function test_getUniqueImpIds(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $subjImpArray = $propDao->getImpDet(105290);
        $this->assertEquals(2, count(ImpHelper::getUniqueImpIds($subjImpArray)));
    }

    public function test_getPrimaryImpId(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $subjImpArray = $propDao->getImpDet(105290);
        $this->assertEquals('104552', ImpHelper::getPrimaryImpId($subjImpArray));
    }

    public function test_getPrimaryImpCount(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $subjImpArray = $propDao->getImpDet(105290);
        $this->assertEquals(12, ImpHelper::getPrimaryImpCount($subjImpArray));
    }

}
