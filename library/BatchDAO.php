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
     * Batch prop table name
     */
    protected $batchPropTable = 'BATCH_PROP';
    protected $batchPropSettings = 'BATCH_PROP_SETTINGS';

    /**
     * BatchDAO constructor.
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     * @param int $dbport
     */
    public function __construct($host, $username, $password, $database, $production, $dbport=3306){
        // Create connection
        $pdo = new PDO("mysql:host=".$host.";dbname=".$database, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;

        if(!$production){
            $this->batchPropTable = $this->batchPropTable . '_STAGE';
        }

        if(!$production){
            $this->batchPropSettings = $this->batchPropSettings . '_STAGE';
        }

        error_log("Connection settings: ", var_dump($this));
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
        $stmt = $this->pdo->prepare("INSERT INTO ". $this->batchPropTable . " SET prop = ?, completed = 'false';");
        $stmt->bindValue(1, $propId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * @param int $propId
     * @return BatchJob
     */
    public function getBatchJob($propId){
        $batchJob = new BatchJob();
        $stmt = $this->pdo->prepare("SELECT prop, completed, pdfs, prop_mktval,
                                            Low_Sale5, Median_Sale5, High_Sale5,
                                            Low_Sale10, Median_Sale10, High_Sale10, 
                                            Low_Sale15, Median_Sale15, High_Sale15,
                                            Median_Eq11,TotalComps,
                                            Comp1_IndicatedValue,Comp2_IndicatedValue,Comp3_IndicatedValue,
                                            Comp4_IndicatedValue,Comp5_IndicatedValue,Comp6_IndicatedValue,
                                            Comp7_IndicatedValue,Comp8_IndicatedValue,Comp9_IndicatedValue,
                                            Comp10_IndicatedValue
                                            FROM ". $this->batchPropTable . " WHERE prop=?");
        $stmt->bindValue(1, $propId, PDO::PARAM_INT);
        $stmt->execute();

        $stmt->bindColumn(1, $batchJob->propId, PDO::PARAM_INT);
        $stmt->bindColumn(2, $batchJob->batchStatus, PDO::PARAM_STR);
        $stmt->bindColumn(3, $batchJob->pdfs, PDO::PARAM_STR);
        $stmt->bindColumn(4, $batchJob->propMktVal, PDO::PARAM_INT);
        $stmt->bindColumn(5, $batchJob->propLowSale5, PDO::PARAM_INT);
        $stmt->bindColumn(6, $batchJob->propMedSale5, PDO::PARAM_INT);
        $stmt->bindColumn(7, $batchJob->propHighSale5, PDO::PARAM_INT);
        $stmt->bindColumn(8, $batchJob->propLowSale10, PDO::PARAM_INT);
        $stmt->bindColumn(9, $batchJob->propMedSale10, PDO::PARAM_INT);
        $stmt->bindColumn(10, $batchJob->propHighSale10, PDO::PARAM_INT);
        $stmt->bindColumn(11, $batchJob->propLowSale15, PDO::PARAM_INT);
        $stmt->bindColumn(12, $batchJob->propMedSale15, PDO::PARAM_INT);
        $stmt->bindColumn(13, $batchJob->propHighSale15, PDO::PARAM_INT);
        $stmt->bindColumn(14, $batchJob->propMedEq11, PDO::PARAM_INT);
        $stmt->bindColumn(15, $batchJob->totalSalesComps, PDO::PARAM_INT);
        $stmt->bindParam(16, $batchJob->comp1_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(17, $batchJob->comp2_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(18, $batchJob->comp3_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(19, $batchJob->comp4_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(20, $batchJob->comp5_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(21, $batchJob->comp6_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(22, $batchJob->comp7_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(23, $batchJob->comp8_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(24, $batchJob->comp9_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(25, $batchJob->comp10_IndicatedValue, PDO::PARAM_INT);

        $stmt->fetch(PDO::FETCH_BOUND);

        return $batchJob;
    }

    /**
     * @param $status
     * @param $start
     * @param int $limit
     * @return \BatchJob[]
     */
    public function getPagedBatchJobs($status, $start, $limit = 20){
        $stmt = $this->pdo->prepare("SELECT prop FROM ".$this->batchPropTable." WHERE completed=? ORDER BY prop ASC LIMIT ?, ?");
        $stmt->bindValue(1, $this->strbool($status), PDO::PARAM_STR);
        $stmt->bindValue(2, $start, PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();

        $jobs = array();
        while($propId = $stmt->fetchColumn()){
            $jobs[] = $this->getBatchJob($propId);
        }
        return $jobs;
    }

    /**
     * @param boolean $status
     * @return int[]
     */
    public function getBatchJobsPropList($status=false){
        $jobs = array();
        $stmt = $this->pdo->prepare("SELECT prop FROM ".$this->batchPropTable." WHERE completed=?");
        $stmt->bindValue(1, $this->strbool($status), PDO::PARAM_STR);
        $stmt->execute();
        
        while(($job = $stmt->fetchColumn()) !== false){
            $jobs[] = $job;
        }
        return $jobs;
    }

    /**
     * @param BatchJob $batchJob
     * @return bool
     */
    public function updateBatchJob($batchJob){
        $stmt = $this->pdo->prepare("UPDATE ".$this->batchPropTable."  
                                        SET completed = ?,
                                            prop_mktval = ?,
                                            Low_Sale5 = ?,
                                            Median_Sale5 = ?,
                                            High_Sale5 = ?,
                                            Low_Sale10 = ?,
                                            Median_Sale10 = ?,
                                            High_Sale10 = ?,
                                            Low_Sale15 = ?,
                                            Median_Sale15 = ?,
                                            High_Sale15 = ?,
                                            Median_Eq11 = ?,
                                            pdfs = ?,
                                            TotalComps = ?,
                                            ErrorSeen = ?,
                                            Comp1_IndicatedValue =?,
                                            Comp2_IndicatedValue =?,
                                            Comp3_IndicatedValue =?,
                                            Comp4_IndicatedValue =?,
                                            Comp5_IndicatedValue =?,
                                            Comp6_IndicatedValue =?,
                                            Comp7_IndicatedValue =?,
                                            Comp8_IndicatedValue =?,
                                            Comp9_IndicatedValue =?,
                                            Comp10_IndicatedValue =?
                                        WHERE 
                                          prop = ?;");
        $boolStr = $this->strbool($batchJob->batchStatus);
        $stmt->bindParam(1, $boolStr, PDO::PARAM_STR);
        $stmt->bindParam(2, $batchJob->propMktVal, PDO::PARAM_INT);
        $stmt->bindParam(3, $batchJob->propLowSale5, PDO::PARAM_INT);
        $stmt->bindParam(4, $batchJob->propMedSale5, PDO::PARAM_INT);
        $stmt->bindParam(5, $batchJob->propHighSale5, PDO::PARAM_INT);
        $stmt->bindParam(6, $batchJob->propLowSale10, PDO::PARAM_INT);
        $stmt->bindParam(7, $batchJob->propMedSale10, PDO::PARAM_INT);
        $stmt->bindParam(8, $batchJob->propHighSale10, PDO::PARAM_INT);
        $stmt->bindParam(9, $batchJob->propLowSale15, PDO::PARAM_INT);
        $stmt->bindParam(10, $batchJob->propMedSale15, PDO::PARAM_INT);
        $stmt->bindParam(11, $batchJob->propHighSale15, PDO::PARAM_INT);
        $stmt->bindParam(12, $batchJob->propMedEq11, PDO::PARAM_INT);
        $stmt->bindParam(13, $batchJob->pdfs, PDO::PARAM_LOB);
        $stmt->bindParam(14, $batchJob->totalSalesComps, PDO::PARAM_INT);
        $stmt->bindParam(15, $batchJob->errorsIn, PDO::PARAM_STR);
        $stmt->bindParam(16, $batchJob->comp1_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(17, $batchJob->comp2_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(18, $batchJob->comp3_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(19, $batchJob->comp4_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(20, $batchJob->comp5_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(21, $batchJob->comp6_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(22, $batchJob->comp7_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(23, $batchJob->comp8_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(24, $batchJob->comp9_IndicatedValue, PDO::PARAM_INT);
        $stmt->bindParam(25, $batchJob->comp10_IndicatedValue, PDO::PARAM_INT);

        $stmt->bindParam(26, $batchJob->propId, PDO::PARAM_INT);

        return $stmt->execute();
    }
    
    public function deleteBatchJob($propId){
        $stmt = $this->pdo->prepare("DELETE FROM ".$this->batchPropTable." WHERE prop = ?;");
        $stmt->bindValue(1, $propId, PDO::PARAM_INT);

        return $stmt->execute();   
    }

    public function deleteAllBatchJobs(){
        $stmt = $this->pdo->prepare("TRUNCATE ".$this->batchPropTable.";");

        return $stmt->execute();
    }

    /**
     * @return queryContext 
     */
    public function getBatchSettings(){
        $queryContext = new queryContext;

        $query = "SELECT TrimIndicated as trimIndicated,
                              MultiHood as multiHood,
                              IncludeVU as includeVu,
                              IncludeMLS as includeMls,
                              NumPrevYears as prevYear,
                              SqftRangePct as sqftPercent,
                              SqftRangeMin as sqftRangeMin,
                              SqftRangeMax as sqftRangeMax,
                              ClassRange as subClassRange,
                              ClassRangeEnabled as subClassRangeEnabled,
                              SaleRatioEnabled as saleRatioEnabled,
                              SaleRatioMin as saleRatioMin,
                              SaleRatioMax as saleRatioMax,
                              PercentGood as percentGoodRange,
                              PercentGoodEnabled as percentGoodRangeEnabled,
                              PercentGoodMin as percentGoodMin,
                              PercentGoodMax as percentGoodMax,
                              NetAdj as netAdjustAmount,
                              NetAdjEnabled as netAdjustEnabled,
                              ImpLimit as limitToLessImps,
                              LimitTcadScores as limitTcadScores,
                              LimitTcadScoresAmount as limitTcadScoreAmount,
                              TcadScoreLimitMin as limitTcadScoresMin,
                              TcadScoreLimitMax as limitTcadScoresMax,
                              LimitToCurrentYearLowered as limitToCurrYear,
                              GrossAdjFilterEnabled as grossAdjFilterEnabled,
                              ShowTcadScores as showTcadScores,
                              ShowSaleRatios as showSaleRatios,
                              rankByIndicated as rankByIndicated,
                              SaleTypeQ as SaleTypeQ,
                              MaxDisplay as MaxDisplay
                              FROM ". $this->batchPropSettings . " WHERE id=(SELECT max(id) FROM ". $this->batchPropSettings . ");";



        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $stmt->bindColumn(1, $queryContext->trimIndicated, PDO::PARAM_BOOL);
        $stmt->bindColumn(2, $queryContext->multiHood, PDO::PARAM_BOOL);
        $stmt->bindColumn(3, $queryContext->includeVu, PDO::PARAM_BOOL);
        $stmt->bindColumn(4, $queryContext->includeMls, PDO::PARAM_BOOL);
        $stmt->bindColumn(5, $queryContext->prevYear, PDO::PARAM_INT);
        $stmt->bindColumn(6, $queryContext->sqftPercent, PDO::PARAM_INT);
        $stmt->bindColumn(7, $queryContext->sqftRangeMin, PDO::PARAM_INT);
        $stmt->bindColumn(8, $queryContext->sqftRangeMax, PDO::PARAM_INT);
        $stmt->bindColumn(9, $queryContext->subClassRange, PDO::PARAM_INT);
        $stmt->bindColumn(10, $queryContext->subClassRangeEnabled, PDO::PARAM_BOOL);
        $stmt->bindColumn(11, $queryContext->saleRatioEnabled, PDO::PARAM_BOOL);
        $stmt->bindColumn(12, $ratioMin, PDO::PARAM_STR);
        $stmt->bindColumn(13, $ratioMax, PDO::PARAM_STR);
        $stmt->bindColumn(14, $queryContext->percentGoodRange, PDO::PARAM_INT);
        $stmt->bindColumn(15, $queryContext->percentGoodRangeEnabled, PDO::PARAM_BOOL);
        $stmt->bindColumn(16, $queryContext->percentGoodMin, PDO::PARAM_INT);
        $stmt->bindColumn(17, $queryContext->percentGoodMax, PDO::PARAM_INT);
        $stmt->bindColumn(18, $queryContext->netAdjustAmount, PDO::PARAM_INT);
        $stmt->bindColumn(19, $queryContext->netAdjustEnabled, PDO::PARAM_BOOL);
        $stmt->bindColumn(20, $queryContext->limitToLessImps, PDO::PARAM_BOOL);
        $stmt->bindColumn(21, $queryContext->limitTcadScores, PDO::PARAM_BOOL);
        $stmt->bindColumn(22, $queryContext->limitTcadScoresAmount, PDO::PARAM_INT);
        $stmt->bindColumn(23, $queryContext->tcadScoreLimitMin, PDO::PARAM_INT);
        $stmt->bindColumn(24, $queryContext->tcadScoreLimitMax, PDO::PARAM_INT);
        $stmt->bindColumn(25, $queryContext->grossAdjFilterEnabled, PDO::PARAM_BOOL);
        $stmt->bindColumn(26, $queryContext->grossAdjFilterEnabled, PDO::PARAM_BOOL);
        $stmt->bindColumn(27, $queryContext->showTcadScores, PDO::PARAM_BOOL);
        $stmt->bindColumn(28, $queryContext->showSaleRatios, PDO::PARAM_BOOL);
        $stmt->bindColumn(29, $rankByIndicated, PDO::PARAM_INT);
        $stmt->bindColumn(30, $boolSaleQ, PDO::PARAM_BOOL);
        $stmt->bindColumn(31, $queryContext->compsToDisplay, PDO::PARAM_INT);

        $stmt->fetch(PDO::FETCH_BOUND);

        if($boolSaleQ){
            $queryContext->salesTypes=['Q'];
        } else {
            $queryContext->salesTypes=[];
        }
        $queryContext->saleRatioMin = floatval($ratioMin);
        $queryContext->saleRatioMax = floatval($ratioMax);

        if($rankByIndicated){
            $queryContext->rank = RankType::Indicated;
        } else {
            $queryContext->rank = RankType::TCAD;
        }

        return $queryContext;
    }

    /**
     * @param queryContext $queryContext
     * @return queryResult
     * @throws Exception
     */
    public function updateBatchSettings($queryContext)
    {
        throw new Exception("Not expecting this to be called");

        $stmt = $this->pdo->prepare("INSERT INTO ". $this->batchPropSettings .
                    " SET TrimIndicated = ?, MultiHood = ?, IncludeVU = ?, IncludeMLS = ?, NumPrevYears = ?, 
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
