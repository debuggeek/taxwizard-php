<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/24/16
 * Time: 9:57 AM
 */

use Apache\Log4php\Logger;

include_once '../library/queryContext.php';
include_once '../library/FullTable.php';
include_once '../library/presentation.php';

use PHPUnit\Framework\TestCase;


class FullTableTest extends TestCase
{
//    /* @var logger $logger */
//    private $logger;
//
//    public function setup(){
//        $this->logger = Logger::getLogger("main");
//    }

    private function defaultQueryContext(){
        $queryContext = new queryContext();
        $queryContext->isEquityComp = false;
        $queryContext->compsToDisplay = 2;
        $queryContext->sqftPercent= 10;
        $queryContext->prevYear=2;
        $queryContext->showBaseMktData = true;
        $queryContext->showTcadScores = true;
        $queryContext->showSaleRatios = true;

        return $queryContext;
    }

    public function test_GenerateFullTable(){
        $queryContext = $this->defaultQueryContext();
        $queryContext->subjPropId = 138282;


        $fullTable = new FullTable();
        $fullTable->generateTableData($queryContext);


        $this->assertNotEmpty($fullTable->getSubjCompArray());
        $this->assertNotEmpty($fullTable->getMedianVal());
        $this->assertNotEmpty($fullTable->getSubjectProp());
        $this->assertNotEmpty($fullTable->getMeanVal());
        $this->assertNotEmpty($fullTable->getMeanValSqft());
        $this->assertNotEmpty($fullTable->getMedianValSqft());

        print generateJsonRows($fullTable);
    }

    public function test_GenerateFullTable_Sales(){
        $queryContext = new queryContext();
        $queryContext->subjPropId = 105290;
        $queryContext->isEquityComp = false;
        $queryContext->compsToDisplay=2;


        $fullTable = new FullTable();
        $fullTable->generateTableData($queryContext);


        $this->assertNotEmpty($fullTable->getSubjCompArray());
        $this->assertNotEmpty($fullTable->getMedianVal());
        $this->assertNotEmpty($fullTable->getSubjectProp());
        $this->assertNotEmpty($fullTable->getMeanVal());
        $this->assertNotEmpty($fullTable->getMeanValSqft());
        $this->assertNotEmpty($fullTable->getMedianValSqft());

        print generateJsonRows($fullTable);
    }

    public function test_GenerateFullTable_Equity(){
        $queryContext = new queryContext();
        $queryContext->subjPropId = 105290;
        $queryContext->isEquityComp = true;
        $queryContext->compsToDisplay=2;


        $fullTable = new FullTable();
        $fullTable->generateTableData($queryContext);


        $this->assertNotEmpty($fullTable->getSubjCompArray());
        $this->assertNotEmpty($fullTable->getMedianVal());
        $this->assertNotEmpty($fullTable->getSubjectProp());
        $this->assertNotEmpty($fullTable->getMeanVal());
        $this->assertNotEmpty($fullTable->getMeanValSqft());
        $this->assertNotEmpty($fullTable->getMedianValSqft());

        print generateJsonRows($fullTable);
    }

    public function test_GenerateFullTable_Sales_J2600Hood(){
        $queryContext = new queryContext();
        $queryContext->subjPropId = 709677;
        $queryContext->isEquityComp = false;
        $queryContext->compsToDisplay=8;
        $queryContext->traceComps=true;
        $queryContext->sqftPercent=20;


        $fullTable = new FullTable();
        $fullTable->generateTableData($queryContext);

        $this->assertNotEmpty($fullTable->getSubjCompArray());
        $this->assertNotEmpty($fullTable->getMedianVal());
        $this->assertNotEmpty($fullTable->getSubjectProp());
        $this->assertNotEmpty($fullTable->getMeanVal());
        $this->assertNotEmpty($fullTable->getMeanValSqft());
        $this->assertNotEmpty($fullTable->getMedianValSqft());

        print generateJsonRows($fullTable);
    }
}
