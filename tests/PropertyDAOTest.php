<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/2/16
 * Time: 4:36 PM
 */

include_once "../library/PropertyDAO.php";
require_once "../library/queryContext.php";

class PropertyDAOTest extends PHPUnit_Framework_TestCase
{
    var $HOST = 'localhost';
    var $user = "root";
    var $pw = "root";
    var $db = "TCAD_2017_2";

    public function test_getPropertyById(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $property = $propDao->getPropertyById(710420);
        var_dump($property);
        $this->assertNotEmpty($property);
    }

    public function test_getImpDet(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $impArray = $propDao->getImpDet(105290);
        var_dump($impArray);
        $this->assertNotEmpty($impArray);
        $this->assertEquals(13, count($impArray));
    }

    public function test_oddSecondary(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $newStyleProp = $propDao->getPropertyById(508783);

        print json_encode($newStyleProp,JSON_PRETTY_PRINT);
    }

    public function testGetHoodProperties(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $queryContext = new queryContext();
        $queryContext->multiHood = false;
        $queryContext->isEquityComp = false;

        $props = $propDao->getHoodProperties('M5200', $queryContext);

        $this->assertCount(54, $props);
    }

    public function testGetHoodProperties_Multi(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $queryContext = new queryContext();
        $queryContext->multiHood = true;
        $queryContext->isEquityComp = false;

        $props = $propDao->getHoodProperties('K1000', $queryContext);

        $this->assertCount(94, $props);
    }

    public function test_MktLevelerAdj(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $property = $propDao->getPropertyById(138282);
        $this->assertEquals(48877, $property->getMktLevelerDetailAdj());
    }

    public function test_getPropertyByIdNoFailedImps(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $property = $propDao->getPropertyById(100944);
        var_dump($property);
        $this->assertNotEmpty($property);
    }
}
