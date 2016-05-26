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
        $this->assertEquals(false, $jobs[0]->batchStatus);

        // Now get our test prop
        $testJob = $batchDao->getBatchJob($propId);
        $testJob->batchStatus = true;
        $testJob->propMktVal = 987654321;
        $testJob->propMedSale5 = 987654321;
        $testJob->propMedSale10 = 987654321;
        $testJob->propMedSale15 = 987654321;
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
