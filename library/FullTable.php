<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/24/16
 * Time: 7:26 AM
 */
include_once 'functions.php';

class FullTable
{

    private $subjectProp;

    private $subjCompArray;

    private $meanVal;

    private $medianVal;

    private $meanValSqft;

    private $medianValSqft;

    private $showBaseMktData;

    private $showTcadScores;

    private $showSaleRatios;

    /**
     * @return propertyClass
     */
    public function getSubjectProp()
    {
        return $this->subjectProp;
    }

    /**
     * @return propertyClass[]
     */
    public function getSubjCompArray()
    {
        return $this->subjCompArray;
    }

    /**
     * Should never need to set directly, added to aid in testing
     * @param $array
     */
    public function setSubjCompArray($array){
        $this->subjCompArray = $array;
    }

    /**
     * @param int $meanVal
     */
    public function setMeanVal(int $meanVal)
    {
        $this->meanVal = $meanVal;
    }

    /**
     * @return int
     */
    public function getMeanVal() : int
    {
        return $this->meanVal;
    }

    /**
     * @param int $medianVal
     */
    public function setMedianVal(int $medianVal)
    {
        $this->medianVal = $medianVal;
    }

    /**
     * @return int
     */
    public function getMedianVal() : int
    {
        return $this->medianVal;
    }

    /**
     * @param float $meanValSqft
     */
    public function setMeanValSqft(float $meanValSqft)
    {
        $this->meanValSqft = $meanValSqft;
    }

    /**
     * @return float
     */
    public function getMeanValSqft() : float
    {
        return $this->meanValSqft;
    }

    /**
     * @param float $medianValSqft
     */
    public function setMedianValSqft(float $medianValSqft)
    {
        $this->medianValSqft = $medianValSqft;
    }

    /**
     * @return float
     */
    public function getMedianValSqft() : float
    {
        return $this->medianValSqft;
    }

    /**
     * @return int
     */
    public function getNumComp(){
        if($this->subjCompArray === null){
            return 0;
        }
        return count($this->subjCompArray)-1; // take away subject
    }

    /**
     * @return mixed
     */
    public function getShowBaseMktData()
    {
        return $this->showBaseMktData;
    }

    /**
     * @param mixed $showBaseMktData
     */
    public function setShowBaseMktData($showBaseMktData)
    {
        $this->showBaseMktData = $showBaseMktData;
    }

    /**
     * @return bool
     */
    public function getShowTcadScores() : bool
    {
        return $this->showTcadScores;
    }

    /**
     * @param bool $showTcadScores
     */
    public function setShowTcadScores($showTcadScores)
    {
        $this->showTcadScores = $showTcadScores;
    }

    /**
     * @return bool
     */
    public function getShowSaleRatios() : bool
    {
        return $this->showSaleRatios;
    }

    /**
     * @param bool $setShowSaleRatios
     */
    public function setShowSaleRatios($setShowSaleRatios)
    {
        $this->showSaleRatios = $setShowSaleRatios;
    }

    /**
     * @param  queryContext $queryContext
     * @throws Exception
     * @internal param propertyClass[] $compInfo
     */
    public function generateTableData($queryContext){
        if($queryContext->subjPropId === null){
            error_log("Must provide subject property id to generate table data");
            throw new Exception("Must provide subject property id in queryContext to generate table data");
        }
        
        $this->subjectProp = getSubjProperty($queryContext->subjPropId);

        //Determine if we should comp off provided list or not
        if(count($queryContext->compInfo) === 0){
            $this->subjCompArray = $this->mineForComps($queryContext);
        } else {
            $this->subjCompArray = $this->useProvidedComps($queryContext);
        }


        if($this->subjCompArray === null || sizeof($this->subjCompArray) == 1){
            $this->subjCompArray = null;
            error_log("No comps found for " . $this->subjectProp->getPropID());
            return;
        }

        $this->setShowBaseMktData($queryContext->showBaseMktData);
        $this->setShowTcadScores($queryContext->showTcadScores);
        $this->setShowSaleRatios($queryContext->showSaleRatios);

        $this->setMeanVal(getMeanVal($this->subjCompArray));
        $this->setMeanValSqft(getMeanValSqft($this->subjCompArray));
        $this->setMedianVal(getMedianVal($this->subjCompArray));
        $this->setMedianValSqft(getMedianValSqft($this->subjCompArray));
    }

    /**
     * @param $queryContext queryContext
     * @return propertyClass[]
     * @throws Exception
     */
    private function mineForComps($queryContext){
        if($queryContext->isEquityComp) {
            error_log("Finding best EQUITY comps for " . $this->subjectProp->getPropID());
        } else {
            error_log("Finding best SALES comps for " . $this->subjectProp->getPropID());
        }

        //no comps provided so we must find some
        $subjCompArray  = generateArrayOfBestComps( $this->subjectProp , $queryContext);

        if($queryContext->traceComps) error_log("TRACE\tFound ".count($this->subjCompArray)." comps after filtering");
        if($queryContext->userFilterEnabled){
            $subjCompArray = $this->applyUserFilter($queryContext, $subjCompArray);
        }
        return $subjCompArray;
    }

    /**
     * @param $queryContext queryContext
     * @param $subjCompArrayIn propertyClass[]
     * @return propertyClass[]
     */
    private function applyUserFilter($queryContext, $subjCompArrayIn)
    {
        $result = array();

        foreach ($subjCompArrayIn as $prop){
            if($prop->isSubj()){
                //preserve the subj
                $result[] = $prop;
                continue;
            }
            if(in_array($prop->getPropID(), $queryContext->filterProps)){
                if($queryContext->filterTypeExclude){
                    $msg = sprintf("%u removed as potential comp due to being in exclusion list", $prop->getPropID());
                    if ($queryContext->traceComps) error_log("TRACE\tgenerateTableData: ".$msg);
                    $queryContext->responseCtx->infos[] = $msg;
                } else {
                    $result[] = $prop;
                }
            } else  {
                if($queryContext->filterTypeExclude){
                    $result[] = $prop;
                } else {
                    $msg = sprintf("%u removed as potential comp due to being not being on inclusion list", $prop->getPropID());
                    if ($queryContext->traceComps) error_log("TRACE\tgenerateTableData: ".$msg);
                    $queryContext->responseCtx->infos[] = $msg;
                }
            }

        }
        return $result;
    }

    /**
     * @param $queryContext queryContext
     * @return propertyClass[]
     * @throws Exception
     */
    private function useProvidedComps($queryContext){
        error_log("Building comps from user provided for ". $this->subjectProp ->getPropID());

        $subjcomparray = array();
        $subjcomparray[] = $this->subjectProp;
        foreach($queryContext->compInfo as $compIn){
            if(in_array($compIn['id'], $queryContext->excludes)){
                error_log("Removing ".$compIn['id']." from comp results due to being in excludes");
                continue;
            }
            $c = getProperty($compIn['id']);
            if($c === null){
                error_log("Unable to retrieve comp property=".$compIn['id']);
            }
            if(!$queryContext->isEquityComp) {
                $c->setSalePrice($compIn['salePrice']);
                $c->mSaleDate = $compIn['saleDate'];
            }
            calcDeltas($this->subjectProp,$c, $queryContext->isEquityComp);
            $subjcomparray[] = $c;
        }
        return $subjcomparray;
    }

    /**
     * Returns a FullTable object with a subjCompArray object that is the size or less of passed in count
     * @param int $count
     * @return FullTable
     */
    public function trimTo($count)
    {
        if ($this->getNumComp() < $count) {
            return $this;
        }
        $newTable = new FullTable();
        $newTable->subjectProp = $this->subjectProp;
        $newTable->subjCompArray = array_slice($this->subjCompArray, 0, $count);

        $newTable->setShowTcadScores($this->getShowTcadScores());
        $newTable->setShowSaleRatios($this->getShowSaleRatios());

        $newTable->setMeanVal(getMeanVal($newTable->subjCompArray));
        $newTable->setMeanValSqft(getMeanValSqft($newTable->subjCompArray));
        $newTable->setMedianVal(getMedianVal($newTable->subjCompArray));
        $newTable->setMedianValSqft(getMedianValSqft($newTable->subjCompArray));

        return $newTable;
    }
}