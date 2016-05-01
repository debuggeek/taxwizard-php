<?php

class queryContext {

    /*
     * Following are all persisted in Batch Table
     */
    public $trimIndicated = false;
    public $includeMls = false;
    public $multiHood = false;
    public $includeVu = false;
    public $prevYear = 1;
    public $sqftPercent = 75;
    public $subClassRange = 2;
    public $subClassRangeEnabled = false;
    public $percentGoodRange = 10;
    public $percentGoodRangeEnabled = false;
    public $netAdjustEnabled = false;
    public $netAdjustAmount = 0;
    public $limitToLessImps = false;
    public $showTcadScores = true;
    public $limitTcadScores = true;
    public $limitTcadScoresAmount = 90;

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
            $this->sqftPercent = trim($getContext['sqftPct']);
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
                $this->percentGoodRange = trim($getContext['pctGoodRange']);
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
            }
        }

        if(isset($getContext['limitImps'])){
            if(strcmp($getContext['limitImps'], 'on') == 0) {
                $this->limitToLessImps = true;
            }
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
                $this->limitTcadScoresAmount = trim($getContext['$limitTcadScoresAmount']);
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