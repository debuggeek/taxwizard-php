<?php

class queryContext {

    /*
     * Following are all persisted in Batch Table
     */

    // The first grouping are all used in search filtering
    public $trimIndicated = false;
    public $includeMls = false;
    public $multiHood = false;
    public $includeVu = false;
    public $prevYear = 1;

    public $sqftPercent = null;
    public $sqftRangeMin = null;
    public $sqftRangeMax = null;

    public $subClassRange = 2;
    public $subClassRangeEnabled = false;

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
    // Group used for display filtering
    public $showTcadScores = true;
    public $displayRatios = false;


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

    public function parseQueryString($getContext){
        //Parse Inputs
        if(isset($getContext['multiyear'])){
            $this->prevYear = $getContext['multiyear'];
        }

        if(isset($getContext['display'])){
            $this->compsToDisplay = intval(trim($getContext['display']));
        }
        if(isset($getContext['propid'])){
            $this->subjPropId = trim($getContext['propid']);
        }
        if(isset($getContext['trimindicated'])){
            if (trim($getContext['trimindicated']) == 'on'){
                $this->trimIndicated = true;
            }
        }
        if(isset($getContext['style'])){
            if (trim($getContext['style']) == 'sales'){
                $this->isEquityComp = false;
            }
        }
        if(isset($getContext['includemls'])){
            if (trim($getContext['includemls']) == 'on'){
                $this->includeMls = true;
            }
        }
        if(isset($getContext['multihood'])){
            if (trim($getContext['multihood']) == 'on'){
                $this->multiHood = true;
            }
        }

        if(isset($getContext['includevu'])) {
            if (trim($getContext['includevu']) == 'on'){
                $this->includeVu = true;
            }
        }

        if(isset($getContext['sqftPct'])){
          $val =  trim($getContext['sqftPct']);
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

        if(isset($getContext['rangeEnabled'])){
            if(strcmp($getContext['rangeEnabled'],'on') == 0){
                $this->subClassRangeEnabled = true;
                $this->subClassRange = trim($getContext['range']);
            }
        }

        if(isset($getContext['pctGoodRangeEnabled'])){
            if(strcmp($getContext['pctGoodRangeEnabled'], 'on') ==0 ) {
                $this->percentGoodRangeEnabled = true;

                $val =  trim($getContext['pctGoodRange']);
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

        if(isset($getContext['netadjust'])){
            if(strcmp($getContext['netadjust'], 'on') ==0 ) {
                $this->netAdjustEnabled = true;
                $this->netAdjustAmount = trim($getContext['netAdjustAmt']);
            }
        }

        if(isset($getContext['exclude'])){
            $excludeStrList = trim($getContext['exclude']);
            $this->excludes = explode('_',$excludeStrList);
        }

        if(isset($getContext['Submit'])){
            if($getContext['Submit'] == 'Build Sales Table'){
                $this->isEquityComp = false;
            } else if(strpos($getContext['Submit'], 'Equity') !== false){
                $this->isEquityComp = true;
            }
        }

        if(isset($getContext['limitImps'])){
            if(strcmp($getContext['limitImps'], 'on') == 0) {
                $this->limitToLessImps = true;
            }
        }

        if(isset($getContext['tracecomps'])){
            $this->traceComps = true;
        }

        if(isset($getContext['showTcadScores'])){
            if(strcmp($getContext['showTcadScores'], 'on') == 0) {
                $this->showTcadScores = true;
            } else {
                $this->showTcadScores = false;
            }
        } else {
            $this->showTcadScores = false;
        }

        if(isset($getContext['limitTcadScores'])){
            if(strcmp($getContext['limitTcadScores'], 'on') ==0 ) {
                $this->limitTcadScores = true;

                $val =  trim($getContext['limitTcadScoresAmount']);
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
