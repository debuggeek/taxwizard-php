<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/9/16
 * Time: 6:57 PM
 */
include_once "../library/functions.php";
include_once "../library/presentation.php";
include_once "../library/responseContext.php";
include_once "../library/FullTable.php";

use PHPUnit\Framework\TestCase;


class presentationTest extends TestCase
{
    public $subjId = 105290;
    public $compId = 106033;
    public $compId2 = 104531;

    protected function setUp()
    {
        global $debug;
    }

    /**
     * @throws Exception
     */
    public function test_generateJsonRows(){
        global $fieldsofinteresteq;
        $responseCtx = new responseContext();
        $subjProperty = getSubjProperty($this->subjId);

        error_log("Building subjcomparray for ".$this->subjId);
        $subjcomparray = array();
        $subjcomparray[0] = $subjProperty;

        $c = getProperty($this->compId);
        $c->setSalePrice(599500);
        $c->mSaleDate = '2/18/2014';
        calcDeltas($subjProperty,$c, false);
        $subjcomparray[] = $c;
        
        $fullTable = new FullTable();
        $fullTable->setSubjCompArray($subjcomparray);

        $fullTable->setMeanVal(getMeanVal($subjcomparray));
        $fullTable->setMeanValSqft(getMeanValSqft($subjcomparray));
        $fullTable->setMedianVal(getMedianVal($subjcomparray));
        $fullTable->setMedianValSqft(getMedianValSqft($subjcomparray));

        $output = generateJsonRows($fullTable,true, $responseCtx);

        echo $output;

        $this->assertEquals("No error", json_last_error_msg());
    }

    /**
     * @throws Exception
     */
    public function test_getMaxPrimaryImpCount(){
        global $fieldsofinteresteq, $SEGMENTSADJ;

        $subjProperty = getSubjProperty($this->subjId);

        $subjcomparray = array();
        $subjcomparray[0] = $subjProperty;

        $c = getProperty($this->compId);
        calcDeltas($subjProperty,$c, false);
        $subjcomparray[] = $c;

        $c2 = getProperty($this->compId2);
        calcDeltas($subjProperty,$c, false);
        $subjcomparray[] = $c2;

        $this->assertEquals(17, getMaxPrimaryImpCount($subjcomparray));
    }

    /**
     * @throws Exception
     */
    public function test_addPrimaryImprovements(){
        global $fieldsofinteresteq, $SEGMENTSADJ;

        $subjProperty = getSubjProperty($this->subjId);

        $subjcomparray = array();
        $subjcomparray[0] = $subjProperty;

        $c = getProperty($this->compId);
        calcDeltas($subjProperty,$c, false);
        $subjcomparray[] = $c;

        $c2 = getProperty($this->compId2);
        calcDeltas($subjProperty,$c2, false);
        $subjcomparray[] = $c2;

        print json_encode(addPrimaryImprovements($subjcomparray, $SEGMENTSADJ), JSON_PRETTY_PRINT);
    }

    /**
     * @throws Exception
     */
    public function test_addSecondaryImprovements(){
        global $fieldsofinteresteq;

        $subjProperty = getSubjProperty($this->subjId);

        $subjcomparray = array();
        $subjcomparray[0] = $subjProperty;

        $c = getProperty($this->compId);
        calcDeltas($subjProperty,$c, false);
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
        calcDeltas($subjProperty,$c, false);
        $subjcomparray[] = $c;

        $result = json_encode($subjcomparray);
        $expected  = "[{\"description\":\"Secondary Imp\",\"col1\":{\"value\":2871,\"delta\":null},\"col2\":{\"value\":0,\"delta\":2871}}]";

        $this->assertEquals($expected, $result);
    }
}
