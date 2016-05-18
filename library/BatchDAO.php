<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/21/16
 * Time: 9:55 PM
 */
include_once "defines.php";
include_once "BatchJob.php";

class BatchDAO
{
    /**
     * @var mysqli
     */
    protected $pdo;

    /**
     * @var
     */
    protected $db;

    /**
     * BatchDAO constructor.
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     * @param int $dbport
     */
    public function __construct($host, $username, $password, $database, $dbport=3306){
        // Create connection
        $pdo = new PDO("mysql:host=".$host.";dbname=".$database, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
    }


    /**
     * @return queryResultupdate
     */
    protected function doSqlQuery($query){
        global $debugquery;

        if($debugquery) error_log("query:".$query);
        $result=$this->pdo->query($query);
        if($debugquery){
            if (!$result){
                error_log("false query came back:".$result);
            } else {
                error_log("query came back:".var_dump($result));
            }
        }
        return $result;
    }

    /**
     * @param int $propId
     * @return false if failed
     */
    public function createBatchJob($propId){
        $stmt = $this->pdo->prepare("INSERT INTO BATCH_PROP SET prop = ?, completed = 'false';");
        $stmt->bindValue(1, $propId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * @param int $propId
     * @return BatchJob
     */
    public function getBatchJob($propId){
        $batchJob = new BatchJob();
        $stmt = $this->pdo->prepare("SELECT prop, completed, pdfs, 
                                            prop_mktval, Median_Sale5, Median_Sale10, 
                                            Median_Sale15, Median_Eq11 FROM BATCH_PROP WHERE prop=?");
        $stmt->bindValue(1, $propId, PDO::PARAM_INT);
        $stmt->execute();

        $stmt->bindColumn(1, $batchJob->propId, PDO::PARAM_INT);
        $stmt->bindColumn(2, $batchJob->batchStatus, PDO::PARAM_STR);
        $stmt->bindColumn(3, $batchJob->pdfs, PDO::PARAM_STR);
        $stmt->bindColumn(4, $batchJob->propMktVal, PDO::PARAM_INT);
        $stmt->bindColumn(5, $batchJob->propMedSale5, PDO::PARAM_INT);
        $stmt->bindColumn(6, $batchJob->propMedSale10, PDO::PARAM_INT);
        $stmt->bindColumn(7, $batchJob->propMedSale15, PDO::PARAM_INT);
        $stmt->bindColumn(8, $batchJob->propMedEq11, PDO::PARAM_INT);

        $stmt->fetch(PDO::FETCH_BOUND);

        return $batchJob;
    }
    /**
     * @param string $status
     * @return int[]
     */
    public function getBatchJobs($status=false){
        $jobs = array();
        $stmt = $this->pdo->prepare("SELECT prop FROM BATCH_PROP WHERE completed=?");
        $stmt->bindValue(1, $this->strbool($status), PDO::PARAM_STR);
        $stmt->execute();
        
        while($job = $stmt->fetchColumn()){
            $jobs[] = $job;
        }
        return $jobs;
    }

    /**
     * @param BatchJob $batchJob
     * @return bool
     */
    public function updateBatchJob($batchJob){
        $stmt = $this->pdo->prepare("UPDATE BATCH_PROP 
                                        SET completed = ?,
                                            prop_mktval = ?,
                                            Median_Sale5 = ?,
                                            Median_Sale10 = ?,
                                            Median_Sale15 = ?,
                                            Median_Eq11 = ?,
                                            pdfs = ?,
                                            TotalComps = ?
                                        WHERE 
                                          prop = ?;");
        $boolStr = $this->strbool($batchJob->batchStatus);
        $stmt->bindParam(1, $boolStr, PDO::PARAM_STR);
        $stmt->bindParam(2, $batchJob->propMktVal, PDO::PARAM_INT);
        $stmt->bindParam(3, $batchJob->propMedSale5, PDO::PARAM_INT);
        $stmt->bindParam(4, $batchJob->propMedSale10, PDO::PARAM_INT);
        $stmt->bindParam(5, $batchJob->propMedSale15, PDO::PARAM_INT);
        $stmt->bindParam(6, $batchJob->propMedEq11, PDO::PARAM_INT);
        $stmt->bindParam(7, $batchJob->pdfs, PDO::PARAM_LOB);
        $stmt->bindParam(8, $batchJob->totalSalesComps, PDO::PARAM_INT);
        $stmt->bindParam(9, $batchJob->propId, PDO::PARAM_INT);

        return $stmt->execute();
    }
    
    public function deleteBatchJob($propId){
        $stmt = $this->pdo->prepare("DELETE FROM BATCH_PROP WHERE prop = ?;");
        $stmt->bindValue(1, $propId, PDO::PARAM_INT);

        return $stmt->execute();   
    }

    public function deleteAllBatchJobs(){
        $stmt = $this->pdo->prepare("TRUNCATE BATCH_PROP;");

        return $stmt->execute();
    }

    /**
     * @return queryContext 
     */
    public function getBatchSettings(){
        $queryContext = new queryContext;

        $stmt = $this->pdo->prepare("SELECT TrimIndicated as trimIndicated,
                              MultiHood as multiHood,
                              IncludeVU as includeVu,
                              IncludeMLS as includeMls,
                              NumPrevYears as prevYear,
                              SqftRange as sqftPercent,
                              ClassRange as subClassRange,
                              ClassRangeEnabled as subClassRangeEnabled,
                              PercentGood as percentGoodRange,
                              PercentGoodEnabled as percentGoodRangeEnabled,
                              NetAdj as netAdjustAmount,
                              NetAdjEnabled as netAdjustEnabled,
                              ImpLimit as limitToLessImps
                          FROM BATCH_PROP_SETTINGS 
                          WHERE id=(SELECT max(id) FROM BATCH_PROP_SETTINGS)");
        $stmt->execute();

        $stmt->bindColumn(1, $queryContext->trimIndicated, PDO::PARAM_BOOL);
        $stmt->bindColumn(2, $queryContext->multiHood, PDO::PARAM_BOOL);
        $stmt->bindColumn(3, $queryContext->includeVu, PDO::PARAM_BOOL);
        $stmt->bindColumn(4, $queryContext->includeMls, PDO::PARAM_BOOL);
        $stmt->bindColumn(5, $queryContext->prevYear, PDO::PARAM_INT);
        $stmt->bindColumn(6, $queryContext->sqftPercent, PDO::PARAM_INT);
        $stmt->bindColumn(7, $queryContext->subClassRange, PDO::PARAM_INT);
        $stmt->bindColumn(8, $queryContext->subClassRangeEnabled, PDO::PARAM_BOOL);
        $stmt->bindColumn(9, $queryContext->percentGoodRange, PDO::PARAM_INT);
        $stmt->bindColumn(10, $queryContext->percentGoodRangeEnabled, PDO::PARAM_BOOL);
        $stmt->bindColumn(11, $queryContext->netAdjustAmount, PDO::PARAM_INT);
        $stmt->bindColumn(12, $queryContext->netAdjustEnabled, PDO::PARAM_BOOL);
        $stmt->bindColumn(13, $queryContext->limitToLessImps, PDO::PARAM_BOOL);

        $stmt->fetch(PDO::FETCH_BOUND);

        return $queryContext;
    }

    /**
     * @param queryContext $queryContext
     * @return queryResult
     */
    public function updateBatchSettings($queryContext)
    {
        $stmt = $this->pdo->prepare("INSERT INTO BATCH_PROP_SETTINGS 
                    SET TrimIndicated = ?, MultiHood = ?, IncludeVU = ?, IncludeMLS = ?, NumPrevYears = ?, 
                     SqftRange = ?, ClassRange = ?, ClassRangeEnabled = ?, PercentGood = ?, PercentGoodEnabled = ?,
                     NetAdj = ?, NetAdjEnabled = ?, ImpLimit = ?");

        $stmt->bindValue(1, $queryContext->trimIndicated, PDO::PARAM_BOOL);
        $stmt->bindValue(2, $queryContext->multiHood, PDO::PARAM_BOOL);
        $stmt->bindValue(3, $queryContext->includeVu, PDO::PARAM_BOOL);
        $stmt->bindValue(4, $queryContext->includeMls, PDO::PARAM_BOOL);
        $stmt->bindValue(5, $queryContext->prevYear, PDO::PARAM_INT);
        $stmt->bindValue(6, $queryContext->sqftPercent, PDO::PARAM_INT);
        $stmt->bindValue(7, $queryContext->subClassRange, PDO::PARAM_INT);
        $stmt->bindValue(8, $queryContext->subClassRangeEnabled, PDO::PARAM_BOOL);
        $stmt->bindValue(9, $queryContext->percentGoodRange, PDO::PARAM_INT);
        $stmt->bindValue(10, $queryContext->percentGoodRangeEnabled, PDO::PARAM_BOOL);
        $stmt->bindValue(11, $queryContext->netAdjustAmount, PDO::PARAM_INT);
        $stmt->bindValue(12, $queryContext->netAdjustEnabled, PDO::PARAM_BOOL);
        $stmt->bindValue(13, $queryContext->limitToLessImps, PDO::PARAM_BOOL);

        $stmt->execute();
        return $stmt->errorInfo();
    }

    function strbool($value)
    {
        return $value == true ? 'true' : 'false';
    }

    function toBool($value)
    {
        if ($value == 'true'){
            return true;
        }
        return false;
    }

    function bitBool($value)
    {
        return $value ? 1 : 0;
    }

}