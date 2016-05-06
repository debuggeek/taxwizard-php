<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 5/6/16
 * Time: 3:39 PM
 */
namespace {
    require '../../library/BatchDAO.php';
}

namespace DebugGeek\TaxWizard\Tests\Services {
    use DebugGeek\TaxWizard\Services\BatchService;
    use DebugGeek\TaxWizard\Tests\TestAccountInfo;

    class BatchServiceTest extends \PHPUnit_Framework_TestCase
    {
        private $batchDao;

        public function setUp()
        {
            parent::setUp();
            $this->batchDao = new \BatchDAO(TestAccountInfo::$servername,
                TestAccountInfo::$username,
                TestAccountInfo::$password,
                TestAccountInfo::$database);
        }

        public function testInsertDelete()
        {
            $jobId = 12345;
            $batchService = new BatchService($this->batchDao);

            $this->assertTrue($batchService->insertJob($jobId));

            $this->assertCount(1, $batchService->getPendingJobCount());

            $this->assertTrue($batchService->deleteJob($jobId));

            $this->assertCount(0, $batchService->getPendingJobCount());

        }
    }
}
