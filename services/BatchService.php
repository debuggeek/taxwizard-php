<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 5/6/16
 * Time: 3:18 PM
 */
namespace {
    require_once 'library/defines.php';
    require_once 'library/BatchDAO.php';
}

namespace DebugGeek\TaxWizard\Services {

    class BatchService
    {

        /* @var BatchDAO $batchDao */
        private $batchDao;

        /**
         * BatchService constructor.
         * @param \BatchDAO $batchDaoIn
         */
        public function __construct(\BatchDAO $batchDaoIn = null)
        {
            global $servername, $username, $password, $database, $production;

            if ($batchDaoIn === null) {
                $this->batchDao = new \BatchDAO($servername, $username, $password, $database,$production);
            } else {
                $this->batchDao = $batchDaoIn;
            }
        }

        /**
         * @param $status
         * @param $start
         * @param $limit
         * @return \BatchJob[]
         */
        public function getPagedBatchJobs($status, $start, $limit)
        {
            $debug = false;
            $batchJobs = $this->batchDao->getPagedBatchJobs($status, $start, $limit);
            if($debug){
                error_log("getPagedBatchJobs>> ". var_dump($batchJobs));
            }
            return $batchJobs;
        }

        /**
         * @return int
         */
        public function getCompletedJobCount()
        {
            return count($this->batchDao->getBatchJobsPropList(true));
        }

        /**
         * @return int
         */
        public function getPendingJobCount()
        {
            return count($this->batchDao->getBatchJobsPropList(false));
        }

        /**
         * @return queryContext
         */
        public function getBatchSettings()
        {
            return $this->batchDao->getBatchSettings();
        }

        public function insertJob($propId)
        {
            if ($this->batchDao->createBatchJob($propId) === false) {
                error_log("Unable to insert job for " . $propId);
                throw new Exception("Unable to insert job for " . $propId);
            }
            return true;
        }

        /**
         * @param int $propId
         * @return bool
         * @throws Exception
         */
        public function deleteJob($propId)
        {
            if ($this->batchDao->deleteBatchJob($propId) === false) {
                error_log("Unable to delete job for " . $propId);
                throw new Exception("Unable to delete job for " . $propId);
            }
            return true;
        }

        public function deleteAllJobs()
        {
            if ($this->batchDao->deleteAllBatchJob() === false) {
                error_log("Unable to delete all jobs");
                throw new Exception("Unable to delete all jobs for ");
            }
            return true;
        }
    }
}