<?php

include_once 'enums/RankType.php';

class queryContext {

    /*
     * Following are all persisted in Batch Table
     */

    // The first grouping are all used in search filtering
    public $trimIndicated = false;
    public $multiHood = false;
    public $includeVu = false;
    public $includeMls = false;
    public $prevYear = 1;

    public $sqftPercent = null;
    public $sqftRangeMin = null;
    public $sqftRangeMax = null;

    public $subClassRange = 2;
    public $subClassRangeEnabled = false;

    public $saleRatioEnabled = false;
    public $saleRatioMin = null;
    public $saleRatioMax = null;

    public $percentGoodRangeEnabled = false;
    public $percentGoodRange = 10;
    public $percentGoodMin = null;
    public $percentGoodMax = null;

    public $netAdjustEnabled = false;
    public $netAdjustAmount = 0;
    public $limitToLessImps = false;

    public $limitTcadScores = false;
    public $limitTcadScoresAmount = null;
    public $tcadScoreLimitMin = null;
    public $tcadScoreLimitMax = null;

    public $limitToOnlyCurrentYearLowered = false;
    public $grossAdjFilterEnabled = false;

    // Rank by
    public $rank = RankType::Indicated;

    // Group used for display filtering
    public $showTcadScores = false;
    public $showSaleRatios = true;


    /*
     * Below settings aren't stored in database
     */
    public $limit = null;
    public $compsToDisplay = 100;

    //Holds a list of propertyIds to exclude from consideration
    public $userFilterEnabled = false;
    public $filterProps = array();
    //By default the filter is an exclusion list
    public $filterTypeExclude = true;

    //Treat like equity by default so we use market val and not sales price
    public $isEquityComp = true;

    public $subjPropId = null;

    public $compInfo = array();

    public $traceComps = false;

    public $responseCtx = null;

    public function parseQueryContextJson($inputContext){
        $this->subjPropId = $inputContext->propId;
        $this->trimIndicated = $inputContext->onlyLowerComps;
        $this->includeMls = $inputContext->includeMLS;
        $this->prevYear = $inputContext->mlsMultiYear;
        $this->limitToOnlyCurrentYearLowered = $inputContext->onlyCurrYearLowered;
        $this->multiHood = $inputContext->multiHood;
        $this->limitToLessImps = $inputContext->limitImps;
        $this->netAdjustEnabled = $inputContext->netAdjEnabled;
        $this->netAdjustAmount = $inputContext->netAdjustAmt;
        $this->subClassRangeEnabled = $inputContext->subClassRangeEnabled;
        $this->subClassRange = $inputContext->subClassRange;
        $this->percentGoodRangeEnabled = $inputContext->pctGoodRangeEnabled;
        $this->percentGoodRange = $inputContext->pctGoodRange;
        $this->percentGoodMin = $inputContext->pctGoodMin;
        $this->percentGoodMax = $inputContext->pctGoodMax;
        $this->limitTcadScores = $inputContext->tcadScoreLimitEnabled;
        $this->saleRatioEnabled = $inputContext->ratiosEnabled;
        $this->saleRatioMin = $inputContext->saleRatioMin;
        $this->saleRatioMax = $inputContext->saleRatioMax;
        $this->limitTcadScoresAmount = $inputContext->tcadScoreLimitPct;
        $this->tcadScoreLimitMin = $inputContext->tcadScoreLimitMin;
        $this->tcadScoreLimitMax = $inputContext->tcadScoreLimitMax;
        $this->isEquityComp = $inputContext->isEquity;
        $this->sqftPercent = $inputContext->sqftRangePct;
        $this->sqftRangeMin = $inputContext->sqftRangeMin;
        $this->sqftRangeMax = $inputContext->sqftRangeMax;

        // Ranking
        if($inputContext->rankByIndicated){
            $this->rank = RankType::Indicated;
        } else {
            $this->rank = RankType::TCAD;
        }

        // Display Options
        $this->showTcadScores = $inputContext->showTcadScores;
        $this->showSaleRatios = $inputContext->showRatios;

        $this->compsToDisplay = $inputContext->maxDisplay;

        // Overrides
        $this->filterProps = $inputContext->filterProps;
        if($inputContext->filterTypeExclude !== null) {
            $this->filterTypeExclude = $inputContext->filterTypeExclude;
        }
        if(array_count_values($this->filterProps) > 0){
            $this->userFilterEnabled = true;
        }

        // Other
        $this->traceComps = $inputContext->traceComps;

    }
}
