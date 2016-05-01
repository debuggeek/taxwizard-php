<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/9/16
 * Time: 6:57 PM
 */
include_once "../library/functions.php";
include_once "../library/presentation.php";

class presentationTest extends PHPUnit_Framework_TestCase
{
    public $subjId = 105290;
    public $compId = 106033;
    public $compId2 = 104531;

    protected function setUp()
    {
        global $debug;
    }

    public function test_generateJsonRows(){
        global $fieldsofinteresteq;
        $subjProperty = getSubjProperty($this->subjId);

        error_log("Building subjcomparray for ".$this->subjId);
        $subjcomparray = array();
        $subjcomparray[0] = $subjProperty;

        $c = getProperty($this->compId);
        $c->setSalePrice(599500);
        $c->mSaleDate = '2/18/2014';
        calcDeltas($subjProperty,$c);
        $subjcomparray[] = $c;
        
        $fullTable = array();
        $fullTable["subjComps"] = $subjcomparray;

        $fullTable["meanVal"] = getMeanVal($subjcomparray);
        $fullTable["meanValSqft"] = getMeanValSqft($subjcomparray);
        $fullTable["medianVal"]= getMedianVal($subjcomparray);
        $fullTable["medianValSqft"] = getMedianValSqft($subjcomparray);

        $output = generateJsonRows($fullTable,true);

        echo $output;

        $this->assertEquals("No error", json_last_error_msg());
    }

    public function test_getMaxPrimaryImpCount(){
        global $fieldsofinteresteq, $SEGMENTSADJ;

        $subjProperty = getSubjProperty($this->subjId);

        $subjcomparray = array();
        $subjcomparray[0] = $subjProperty;

        $c = getProperty($this->compId);
        calcDeltas($subjProperty,$c);
        $subjcomparray[] = $c;

        $c2 = getProperty($this->compId2);
        calcDeltas($subjProperty,$c2);
        $subjcomparray[] = $c2;

        $this->assertEquals(17, getMaxPrimaryImpCount($subjcomparray));
    }

    public function test_addPrimaryImprovements(){
        global $fieldsofinteresteq, $SEGMENTSADJ;

        $subjProperty = getSubjProperty($this->subjId);

        $subjcomparray = array();
        $subjcomparray[0] = $subjProperty;

        $c = getProperty($this->compId);
        calcDeltas($subjProperty,$c);
        $subjcomparray[] = $c;

        $c2 = getProperty($this->compId2);
        calcDeltas($subjProperty,$c2);
        $subjcomparray[] = $c2;

        print json_encode(addPrimaryImprovements($subjcomparray, $SEGMENTSADJ), JSON_PRETTY_PRINT);
    }

    public function test_addSecondaryImprovements(){
        global $fieldsofinteresteq;

        $subjProperty = getSubjProperty($this->subjId);

        $subjcomparray = array();
        $subjcomparray[0] = $subjProperty;

        $c = getProperty($this->compId);
        calcDeltas($subjProperty,$c);
        $subjcomparray[] = $c;

        $result = json_encode(addSecondaryImprovements($subjcomparray));
        $expected  = "[{\"description\":\"Secondary Imp\",\"col1\":{\"value\":2871,\"delta\":null},\"col2\":{\"value\":0,\"delta\":2871}}]";

        $this->assertEquals($expected, $result);
    }

    public function test_TCADScore(){
        global $fieldsofinteresteq;

        $subjProperty = getSubjProperty($this->subjId);

        $subjcomparray = array();
        $subjcomparray[0] = $subjProperty;

        $c = getProperty($this->compId);
        calcDeltas($subjProperty,$c);
        $subjcomparray[] = $c;

        $result = json_encode($subjcomparray);
        $expected  = "[{\"description\":\"Secondary Imp\",\"col1\":{\"value\":2871,\"delta\":null},\"col2\":{\"value\":0,\"delta\":2871}}]";

        $this->assertEquals($expected, $result);
    }
}
