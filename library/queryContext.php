<?php

class queryContext {

    public $trimIndicated = false;
    public $limit = null;
    public $compsToDisplay = 100;
    public $includeMls = false;
    public $multiHood = false;
    public $includeVu = false;
    public $prevYear = 1;
    public $sqftPercent = .75;
    public $subClassRange = 2;
    public $subClassRangeEnabled = false;
    public $percentGoodRange = 10;
    public $percentGoodRangeEnabled = false;
    public $netAdjustEnabled = false;
    public $netAdjustAmount = 0;
    
    //Holds a list of propertyIds to exclude from consideration
    public $excludes = array();

    //Treat like equity by default so we use market val and not sales price
    public $isEquityComp = true;

    public $subjPropId = null;
}