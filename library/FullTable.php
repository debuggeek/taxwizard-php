<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/24/16
 * Time: 7:26 AM
 */
include_once 'functions.php';
include_once 'SubjCompArrayUtil.php';

class FullTable
{

    private $subjectProp;

    private $subjCompArray;

    private $lowVal;

    private $highVal;

    private $meanVal;

    private $medianVal;

    private $meanValSqft;

    private $medianValSqft;

    private $showBaseMktData;

    private $showTcadScores;

    private $showSaleRatios;

    private $compIndicatedValues;

    private $totalCompsFound;

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
     * @return int
     */
    public function getLowVal(): int
    {
        return $this->lowVal;
    }

    /**
     * @param int $lowVal
     */
    public function setLowVal(int $lowVal)
    {
        $this->lowVal = $lowVal;
    }

    /**
     * @return int
     */
    public function getHighVal(): int
    {
        return $this->highVal;
    }

    /**
     * @param int $highVal
     */
    public function setHighVal(int $highVal)
    {
        $this->highVal = $highVal;
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
    public function getTotalCompsFound()
    {
        return $this->totalCompsFound;
    }

    /**
     * @param mixed $totalCompsFound
     */
    public function setTotalCompsFound($totalCompsFound): void
    {
        $this->totalCompsFound = $totalCompsFound;
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
     * @return mixed
     */
    public function getCompIndicatedValues()
    {
        return $this->compIndicatedValues;
    }

    /**
     * @param mixed $compIndicatedValues
     */
    public function setCompIndicatedValues($compIndicatedValues): void
    {
        $this->compIndicatedValues = $compIndicatedValues;
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

        $compIndVal = array();
        $count = 0;
        foreach ($this->subjCompArray as $comp){
            $count = $count + 1;
            if($count == 1){
                //skip subject
                continue;
            }
            $compIndVal[$count] = $comp->getIndicatedVal(false);
        }
        $count = $count - 1;  // Don't count subject

        $this->setCompIndicatedValues($compIndVal);

        $this->setShowBaseMktData($queryContext->showBaseMktData);
        $this->setShowTcadScores($queryContext->showTcadScores);
        $this->setShowSaleRatios($queryContext->showSaleRatios);

        FullTable::updateTableCalcs($this);
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
        /** @var responseContext $responseCtx */
        $responseCtx = $queryContext->responseCtx;
        $this->setTotalCompsFound($responseCtx->unfilteredPropCount);

        if($queryContext->traceComps) error_log("TRACE\tFound ".count($subjCompArray)." comps after filtering");
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
        $newTable->setCompIndicatedValues(array_slice($this->getCompIndicatedValues(), 0, $count));

        FullTable::updateTableCalcs($newTable);

        return $newTable;
    }

    private static function updateTableCalcs(FullTable &$table){

        $table->setLowVal(calcLowVal($table->subjCompArray));
        $table->setHighVal(calcHighVal($table->subjCompArray));
        $table->setMeanVal(getMeanVal($table->subjCompArray));
        $table->setMeanValSqft(getMeanValSqft($table->subjCompArray));
        $table->setMedianVal(getMedianVal($table->subjCompArray));
        $table->setMedianValSqft(getMedianValSqft($table->subjCompArray));
    }
}