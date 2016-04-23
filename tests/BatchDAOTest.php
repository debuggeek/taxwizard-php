<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/21/16
 * Time: 10:08 PM
 */

include_once "../library/BatchDAO.php";
include_once "../library/queryContext.php";

class BatchDAOTest extends PHPUnit_Framework_TestCase
{

    var $HOST = 'localhost';
    var $user = "root";
    var $pw = "root";
    var $db = "TCAD_2016";

    public function test_updateBatchSettings(){
        $batchDao = new BatchDAO($this->HOST, $this->user, $this->pw, $this->db);
        $queryContext = new queryContext();

        $this->assertNotFalse($batchDao->updateBatchSettings($queryContext));

        $newQueryContext = $batchDao->getBatchSettings();

        $this->assertEquals($queryContext, $newQueryContext);

        $queryContext->percentGoodRange=42;
        $queryContext->includeMls=true;
        $queryContext->trimIndicated=true;
        $queryContext->multiHood=true;
        $queryContext->includeVu=true;
        $queryContext->subClassRangeEnabled=true;
        $queryContext->percentGoodRangeEnabled=true;
        $queryContext->subClassRange=4;
        $queryContext->sqftPercent=42;
        $queryContext->netAdjustAmount=10000;
        $queryContext->netAdjustEnabled=true;
        $queryContext->prevYear=5;


        $batchDao->updateBatchSettings($queryContext);

        $newQueryContext = $batchDao->getBatchSettings();

        $this->assertEquals($queryContext, $newQueryContext);
    }
}
