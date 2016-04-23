<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/2/16
 * Time: 4:36 PM
 */

include_once "../library/PropertyDAO.php";

class PropertyDAOTest extends PHPUnit_Framework_TestCase
{
    var $HOST = 'localhost';
    var $user = "root";
    var $pw = "root";
    var $db = "TCAD_2016";

    public function test_getPropertyById(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $property = $propDao->getPropertyById(224789);
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
    
    public function test_newAsGoodAsOld(){
        include_once "../library/functions.php";

        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $newStyleProp = $propDao->getPropertyById(105290);
        
        $oldProp = getProperty(105290,false);
        
        $this->assertEquals($oldProp, $newStyleProp);
    }

    public function test_oddSecondary(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $newStyleProp = $propDao->getPropertyById(508783);

        print json_encode($newStyleProp,JSON_PRETTY_PRINT);
    }
}
