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

    private $showTcadScores;

    /**
     * @return propertyClass
     */
    public function getSubjectProp()
    {
        return $this->subjectProp;
    }

    /**
     * @return propertyClass()
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
    public function getMeanVal()
    {
        return $this->meanVal;
    }

    /**
     * @return int
     */
    public function getMedianVal()
    {
        return $this->medianVal;
    }

    /**
     * @return int
     */
    public function getMeanValSqft()
    {
        return $this->meanValSqft;
    }

    /**
     * @return int
     */
    public function getMedianValSqft()
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
     * @return bool
     */
    public function getShowTcadScores()
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
     * @param  queryContext $queryContext
     * @param  array $compInfo
     * @throws Exception
     */
    public function generateTableData($queryContext){
        if($queryContext->subjPropId === null){
            error_log("Must provide subject property id to generate table data");
            throw new Exception("Must provide subject property id in queryContext to generate table data");
        }
        
        $this->subjectProp = getSubjProperty($queryContext->subjPropId);

        if(count($queryContext->compInfo) === 0){
            error_log("Finding best comps for ". $this->subjectProp->getPropID());

            //no comps provided so we must find some
            $this->subjCompArray  = generateArrayOfBestComps( $this->subjectProp , $queryContext);
            
            if(count($queryContext->excludes) > 0){
                //Save off since it might go down
                $startCount = count($this->subjCompArray);
                for($i = 0 ; $i < $startCount; $i++){
                    /* @var propertyClass $property */
                    $property = $this->subjCompArray[$i];
                    if(in_array($property->getPropID(), $queryContext->excludes)){
                        error_log("Removing ".$property->getPropID()." from comp results due to being in excludes");
                        unset($this->subjCompArray[$i]);
                    }
                }
                //Re-index the array so we don't have gaps
                $this->subjCompArray  = array_values($this->subjCompArray);
            }
        } else {
            error_log("Building comps from provided for ". $this->subjectProp ->getPropID());

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
                calcDeltas($this->subjectProp,$c);
                $subjcomparray[] = $c;
            }
            $this->subjCompArray = $subjcomparray;
        }

        if($this->subjCompArray === null || sizeof($this->subjCompArray) == 1){
            $this->subjCompArray = null;
            error_log("No comps found for " . $this->subjectProp->getPropID());
            return;
        }

        $this->setShowTcadScores($queryContext->showTcadScores);

        $this->meanVal = getMeanVal($this->subjCompArray);
        $this->meanValSqft = getMeanValSqft($this->subjCompArray);
        $this->medianVal = getMedianVal($this->subjCompArray);
        $this->medianValSqft = getMedianValSqft($this->subjCompArray);
    }

    /**
     * Returns a FullTable object with a subjCompArray object that is the size or less of passed in count
     * @param int $count
     * @return $this|FullTable
     */
    public function trimTo($count){
        if($this->getNumComp() < $count){
            return $this;
        }
        $newTable = new FullTable();
        $newTable->subjectProp = $this->subjectProp;
        $newTable->subjCompArray = array_slice($this->subjCompArray, 0, $count);

        $newTable->meanVal = getMeanVal($newTable->subjCompArray);
        $newTable->meanValSqft = getMeanValSqft($newTable->subjCompArray);
        $newTable->medianVal = getMedianVal($newTable->subjCompArray);
        $newTable->medianValSqft = getMedianValSqft($newTable->subjCompArray);
        
        return $newTable;
    }
}