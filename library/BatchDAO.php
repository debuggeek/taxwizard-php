<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/21/16
 * Time: 9:55 PM
 */
include_once "defines.php";

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
                              NetAdjEnabled as netAdjustEnabled
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
                     NetAdj = ?, NetAdjEnabled = ?");

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

        $stmt->execute();
        return $stmt->errorInfo();
    }

    function strbool($value)
    {
        return $value ? 'TRUE' : 'FALSE';
    }

    function bitBool($value)
    {
        return $value ? 1 : 0;
    }

}