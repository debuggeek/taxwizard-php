<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/24/16
 * Time: 12:03 PM
 */
class BatchJob
{
    public $propId;
    
    public $batchStatus;
    
    public $pdfs;

    public $propMktVal;

    /**
     * @var int
     */
    public $propLowSale5;

    /**
     * @var int
     */
    public $propMedSale5;

    /**
     * @var int
     */
    public $propHighSale5;

    /**
     * @var int
     */
    public $propLowSale10;

    /**
     * @var int
     */
    public $propMedSale10;

    /**
     * @var int
     */
    public $propHighSale10;

    /**
     * @var int
     */
    public $propLowSale15;

    /**
     * @var int
     */
    public $propMedSale15;

    /**
     * @var int
     */
    public $propHighSale15;

    /**
     * @var int
     */
    public $propMedEq11;

    /**
     * @var int
     */
    public $totalSalesComps;

    /**
     * @var int
     */
    public $comp1_IndicatedValue;

    /**
     * @var int
     */
    public $comp2_IndicatedValue;

    /**
     * @var int
     */
    public $comp3_IndicatedValue;

    /**
     * @var int
     */
    public $comp4_IndicatedValue;

    /**
     * @var int
     */
    public $comp5_IndicatedValue;

    /**
     * @var int
     */
    public $comp6_IndicatedValue;

    /**
     * @var int
     */
    public $comp7_IndicatedValue;

    /**
     * @var int
     */
    public $comp8_IndicatedValue;

    /**
     * @var int
     */
    public $comp9_IndicatedValue;

    /**
     * @var int
     */
    public $comp10_IndicatedValue;

    /**
     * @var string
     */
    public $compType;

    /**
     * @var string
     */
    public $errorsIn;

    function parseArray($array){
        $this->propMktVal = $array['prop_mktvl'];
        $this->propLowSale5 = $array['lowSale5'];
        $this->propMedSale5 = $array['medSale5'];
        $this->propHighSale5 = $array['highSale5'];
        $this->propLowSale10 = $array['lowSale10'];
        $this->propMedSale10 = $array['medSale10'];
        $this->propHighSale10 = $array['highSale10'];
        $this->propLowSale15 = $array['lowSale15'];
        $this->propMedSale15 = $array['medSale15'];
        $this->propHighSale15 = $array['highSale15'];
        $this->propMedEq11 = $array['medEq11'];
        $this->pdfs = $array['base64'];
        $this->totalSalesComps = $array['totalSalesComps'];
        $this->errorsIn = $array['errors'];
        $this->comp1_IndicatedValue = $array['Comp1_IndicatedValue'];
        $this->comp2_IndicatedValue = $array['Comp2_IndicatedValue'];
        $this->comp3_IndicatedValue = $array['Comp3_IndicatedValue'];
        $this->comp4_IndicatedValue = $array['Comp4_IndicatedValue'];
        $this->comp5_IndicatedValue = $array['Comp5_IndicatedValue'];
        $this->comp6_IndicatedValue = $array['Comp6_IndicatedValue'];
        $this->comp7_IndicatedValue = $array['Comp7_IndicatedValue'];
        $this->comp8_IndicatedValue = $array['Comp8_IndicatedValue'];
        $this->comp9_IndicatedValue = $array['Comp9_IndicatedValue'];
        $this->comp10_IndicatedValue = $array['Comp10_IndicatedValue'];
        $this->compType = $array['compType'];
    }
}