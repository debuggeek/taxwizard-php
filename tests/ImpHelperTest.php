<?php

include_once "../library/PropertyDAO.php";
include_once "../library/ImpHelper.php";
include_once "../library/ImprovementDetailClass.php";

class ImpHelperTest extends PHPUnit_Framework_TestCase{

    var $HOST = 'localhost';
    var $user = "root";
    var $pw = "root";
    var $db = "TCAD_2016";

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

    public function test_getPrimaryImprovements(){
        $propDao = new PropertyDAO($this->HOST, $this->user, $this->pw, $this->db);
        $subjImpArray = $propDao->getImpDet(138282);
        $primaryImps = ImpHelper::getPrimaryImprovements($subjImpArray);
        $total = 0;
        foreach ($primaryImps as $imp) {
            $total += $imp->getDetArea();
        }
        $this->assertEquals(3166, $total);
    }

    /*
     * I was seeing the comp get the -162836 taken as a 1st floor delta which is way to high
     */
    public function testCalcDeltaSegments(){
        include_once '../library/functions.php';
        include_once '../library/presentation.php';
        include_once '../library/FullTable.php';

        $subjId = 280345;
        $compId = 279748;

        $subjProp = getSubjProperty($subjId);
        $compProp = getProperty($compId);

        calcDeltas($subjProp, $compProp,true);
        $fullTable = new FullTable();
        $fullTable->setSubjCompArray(array($subjProp, $compProp));
        $result = generateJsonRows($fullTable);

        print $result;
    }

    /*
     * I was seeing the PORCH in the subj and comp were reusing the same
     * improvement detail data for multiple improvements of the same type
     */
    public function testcompareImpDetails_AddDelta_SubjWMultiImpsSameType(){
        include_once '../library/functions.php';
        include_once '../library/presentation.php';
        include_once '../library/FullTable.php';

        $subjId = 187428;
        $compId = 189181;

        $subjProp = getSubjProperty($subjId);
        //var_dump($subjProp->getImpDets());
        $compProp = getProperty($compId);

        calcDeltas($subjProp, $compProp,true);
        $fullTable = new FullTable();
        $fullTable->setSubjCompArray(array($subjProp, $compProp));
        $result = generateJsonRows($fullTable);

        print $result;
    }
}
