<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/21/16
 * Time: 10:08 PM
 */

include_once "../library/BatchDAO.php";
include_once "../library/queryContext.php";

use PHPUnit\Framework\TestCase;

class BatchDAOTest extends TestCase
{

    var $HOST = 'localhost';
    var $user = "root";
    var $pw = "root";
    var $db = "TCAD_2018";

//    public function test_updateBatchSettings(){
//        $batchDao = new BatchDAO($this->HOST, $this->user, $this->pw, $this->db);
//        $queryContext = new queryContext();
//
//        $this->assertNotFalse($batchDao->updateBatchSettings($queryContext));
//
//        $newQueryContext = $batchDao->getBatchSettings();
//
//        $this->assertEquals($queryContext, $newQueryContext);
//
//        $queryContext->percentGoodRange=42;
//        $queryContext->includeMls=true;
//        $queryContext->trimIndicated=true;
//        $queryContext->multiHood=true;
//        $queryContext->includeVu=true;
//        $queryContext->subClassRangeEnabled=true;
//        $queryContext->percentGoodRangeEnabled=true;
//        $queryContext->subClassRange=4;
//        $queryContext->sqftPercent=42;
//        $queryContext->netAdjustAmount=10000;
//        $queryContext->netAdjustEnabled=true;
//        $queryContext->prevYear=5;
//
//
//        $batchDao->updateBatchSettings($queryContext);
//
//        $newQueryContext = $batchDao->getBatchSettings();
//
//        $this->assertEquals($queryContext, $newQueryContext);
//    }

    public function test_GetBatchSettings(){
        $batchDao = new BatchDAO($this->HOST, $this->user, $this->pw, $this->db);
        $queryContext = new queryContext();

        $newQueryContext = $batchDao->getBatchSettings();

        $this->assertNotEmpty($newQueryContext);
    }

    public function test_GetUpdateBatchJobs(){
        $propId = 12345;
        $batchDao = new BatchDAO($this->HOST, $this->user, $this->pw, $this->db);

        //Cleanup in case
        $batchDao->deleteBatchJob($propId);
        $startJobs = $batchDao->getBatchJobsPropList(false);

        $this->assertNotFalse($batchDao->createBatchJob($propId));

        /* @var BatchJob[] $jobs*/
        $jobs = $batchDao->getBatchJobsPropList(false);

        $this->assertEquals(count($startJobs) + 1, count($jobs));

        // Now get our test prop
        $testJob = $batchDao->getBatchJob($propId);
        $this->assertEquals('false', $testJob->batchStatus);

        // Pretend we calculated some things
        $testJob->batchStatus = 'true';
        $testJob->propMktVal = 987654321;
        $testJob->propLowSale5 = 5;
        $testJob->propMedSale5 = 55;
        $testJob->propHighSale5 = 555;
        $testJob->propLowSale10 = 10;
        $testJob->propMedSale10 = 1010;
        $testJob->propHighSale10 = 101010;
        $testJob->propLowSale15 = 15;
        $testJob->propMedSale15 = 1515;
        $testJob->propHighSale15 = 151515;
        $testJob->propMedEq11 = 987654321;
        $testJob->pdfs = base64_encode("987654321");

        $this->assertNotFalse($batchDao->updateBatchJob($testJob));

        $retJob = $batchDao->getBatchJob($propId);
        $this->assertEquals($testJob, $retJob);

        $this->assertNotFalse( $batchDao->deleteBatchJob($propId));
    }

    public function test_GetPagedBatchJobs(){
        $batchDao = new BatchDAO($this->HOST, $this->user, $this->pw, $this->db);

        $jobs = $batchDao->getPagedBatchJobs(true,0,5);
        var_dump($jobs);
        $this->assertEquals(5, sizeof($jobs));

    }
    
}
