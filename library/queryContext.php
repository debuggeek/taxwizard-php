<?php

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
    public $rankByIndicated = true;

    // Group used for display filtering
    public $showBaseMktData = true;
    public $showTcadScores = false;
    public $showSaleRatios = true;


    /*
     * Below settings aren't stored in database
     */
    public $limit = null;
    public $compsToDisplay = 100;

    //Holds a list of propertyIds to exclude from consideration
    public $excludes = array();

    //Treat like equity by default so we use market val and not sales price
    public $isEquityComp = true;

    public $subjPropId = null;

    public $compInfo = array();

    public $traceComps = false;

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
        $this->rankByIndicated = $inputContext->rankByIndicated;

        // Display Options
        $this->showBaseMktData = $inputContext->showBaseMktData;
        $this->showTcadScores = $inputContext->showTcadScores;
        $this->showSaleRatios = $inputContext->showRatios;

        $this->compsToDisplay = $inputContext->maxDisplay;

    }

    public function parseQueryString($inputContextRaw){
       // $inputContext = json_decode($inputContextRaw[0]);
        //Parse Inputs
        if(isset($inputContext['multiyear'])){
            $this->prevYear = $inputContext['multiyear'];
        }

        if(isset($inputContext['display'])){
            $this->compsToDisplay = intval(trim($inputContext['display']));
        }
        if(isset($inputContext['propid'])){
            $this->subjPropId = trim($inputContext['propid']);
        }
        if(isset($inputContext['trimindicated'])){
            if (trim($inputContext['trimindicated']) == 'on'){
                $this->trimIndicated = true;
            }
        }
        if(isset($inputContext['style'])){
            if (trim($inputContext['style']) == 'sales'){
                $this->isEquityComp = false;
            }
        }
        if(isset($inputContext['includemls'])){
            if (trim($inputContext['includemls']) == 'on'){
                $this->includeMls = true;
            }
        }
        if(isset($inputContext['multihood'])){
            if (trim($inputContext['multihood']) == 'on'){
                $this->multiHood = true;
            }
        }

        if(isset($inputContext['includevu'])) {
            if (trim($inputContext['includevu']) == 'on'){
                $this->includeVu = true;
            }
        }

        if(isset($inputContext['sqftPct'])){
          $val =  trim($inputContext['sqftPct']);
          $valArray = explode(":", $val);
          if(count($valArray) == 2){
            $this->sqftRangeMin = $valArray[0];
            $this->sqftRangeMax = $valArray[1];
            $this->sqftPercent = null;
          } else if(count($valArray) == 1) {
            $this->sqftRangeMin = null;
            $this->sqftRangeMax = null;
            $this->sqftPercent = $valArray[0];
          } else {
            error_log("Unexpected query value for sqftPct");
          }
        }

        if(isset($inputContext['rangeEnabled'])){
            if(strcmp($inputContext['rangeEnabled'],'on') == 0){
                $this->subClassRangeEnabled = true;
                $this->subClassRange = intval(trim($inputContext['subClassRange']));
            }
        }

        if(isset($inputContext['saleRatioEnabled'])){
            if(strcmp($inputContext['saleRatioEnabled'], 'on') ==0 ) {
                $this->saleRatioEnabled = true;

                $val =  trim($inputContext['saleRatioRange']);
                $valArray = explode(":", $val);
                if(count($valArray) == 2){
                    $this->saleRatioMin = floatval($valArray[0]);
                    $this->saleRatioMax = floatval($valArray[1]);
                } else {
                    error_log("Unexpected query value for saleRatioEnabled");
                }
            }
        }

        if(isset($inputContext['pctGoodRangeEnabled'])){
            if(strcmp($inputContext['pctGoodRangeEnabled'], 'on') ==0 ) {
                $this->percentGoodRangeEnabled = true;

                $val =  trim($inputContext['pctGoodRange']);
                $valArray = explode(":", $val);
                if(count($valArray) == 2){
                    $this->percentGoodMin = $valArray[0];
                    $this->percentGoodMax = $valArray[1];
                    $this->percentGoodRange = null;
                } else if(count($valArray) == 1) {
                    $this->percentGoodMin = null;
                    $this->percentGoodMax = null;
                    $this->percentGoodRange = $valArray[0];
                } else {
                    error_log("Unexpected query value for pctGoodRange");
                }
            }
        }

        if(isset($inputContext['netadjust'])){
            if(strcmp($inputContext['netadjust'], 'on') ==0 ) {
                $this->netAdjustEnabled = true;
                $this->netAdjustAmount = intVal(trim($inputContext['netAdjustAmt']));
            }
        }

        if(isset($inputContext['exclude'])){
            $excludeStrList = trim($inputContext['exclude']);
            $this->excludes = explode('_',$excludeStrList);
        }

        if(isset($inputContext['Submit'])){
            if($inputContext['Submit'] == 'Build Sales Table'){
                $this->isEquityComp = false;
            } else if(strpos($inputContext['Submit'], 'Equity') !== false){
                $this->isEquityComp = true;
            }
        }

        if(isset($inputContext['limitImps'])){
            if(strcmp($inputContext['limitImps'], 'on') == 0) {
                $this->limitToLessImps = true;
            }
        }

        if(isset($inputContext['tracecomps'])){
            $this->traceComps = true;
        }

        //////////////
        // Rank options
        /////////////
        if(isset($inputContext['rank'])){
            if(strcmp($inputContext['rank'], 'indicated') == 0) {
                $this->rankByIndicated = true;
            } else {
                $this->rankByIndicated = false;
            }
        }

        //////////////
        // Display filter options
        /////////////

        if(isset($inputContext['showBaseMktData'])){
            if(strcmp($inputContext['showBaseMktData'], 'on') == 0) {
                $this->showBaseMktData = true;
            } else {
                $this->showBaseMktData = false;
            }
        }

        if(isset($inputContext['showTcadScores'])){
            if(strcmp($inputContext['showTcadScores'], 'on') == 0) {
                $this->showTcadScores = true;
            } else {
                $this->showTcadScores = false;
            }
        }

        if(isset($inputContext['showSaleRatio'])){
            if(strcmp($inputContext['showSaleRatio'], 'on') == 0) {
                $this->showSaleRatios = true;
            } else {
                $this->showSaleRatios = false;
            }
        }

        if(isset($inputContext['limitTcadScores'])){
            if(strcmp($inputContext['limitTcadScores'], 'on') ==0 ) {
                $this->limitTcadScores = true;

                $val =  trim($inputContext['limitTcadScoresAmount']);
                $valArray = explode(":", $val);
                if(count($valArray) == 2){
                    $this->tcadScoreLimitMin = $valArray[0];
                    $this->tcadScoreLimitMax = $valArray[1];
                    $this->limitTcadScoresAmount = null;
                } else if(count($valArray) == 1) {
                    $this->tcadScoreLimitMin = null;
                    $this->tcadScoreLimitMax = null;
                    $this->limitTcadScoresAmount = $valArray[0];
                } else {
                    error_log("Unexpected query value for limitTcadScoresAmount");
                }
            } else {
                $this->limitTcadScores = false;
            }
        }

        $compInt = 1;
        while(true){
            if(isset($_GET['c'.$compInt])){
                $id = trim($_GET['c'.$compInt]);
                if(isset($_GET['c'.$compInt.'sp'])){
                    $saleprice = trim($_GET['c'.$compInt.'sp']);
                } else {
                    $saleprice = null;
                }
                if(isset($_GET['c'.$compInt.'sd'])){
                    $saledate = trim($_GET['c'.$compInt.'sd']);
                } else {
                    $saledate = null;
                }
                $this->compInfo[] = array("id"=>$id,"salePrice"=>$saleprice,"saleDate"=>$saledate);
                $compInt++;
            } else {
                break;
            }
        }

    }
}
