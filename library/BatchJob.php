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
    public $propMedSale5;

    /**
     * @var int
     */
    public $propMedSale10;

    /**
     * @var int
     */
    public $propMedSale15;

    /**
     * @var int
     */
    public $propMedEq11;

    /**
     * @var int
     */
    public $totalSalesComps;

    function parseArray($array){
        $this->propMktVal = $array['prop_mktvl'];
        $this->propMedSale5 = $array['medSale5'];
        $this->propMedSale10 = $array['medSale10'];
        $this->propMedSale15 = $array['medSale15'];
        $this->propMedEq11 = $array['medEq11'];
        $this->pdfs = $array['base64'];
        $this->totalSalesComps = $array['totalSalesComps'];
    }
}