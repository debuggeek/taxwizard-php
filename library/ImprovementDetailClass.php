<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/2/16
 * Time: 3:57 PM
 */
class ImprovementDetailClass{

    /**
     * Improvement ID
     * @var string
     */
    protected $imprv_id;

    /**
     * Code that represents improvement type
     * @var int
     */
    protected $imprv_det_type_cd;

    /**
     * Description of improvement type
     * @var string
     */
    protected $imprv_det_type_desc;

    /**
     * Base value of improvement, usually used for secondary
     * @var int
     */
    protected $imprv_val;

    /**Improvement Detail Area
     * @var int
     */
    protected $det_area;

    /**
     * Improvement Detail Unit price
     * @var double
     */
    protected $det_unitprice;

    /**
     * Improvement Details Unit price should be used
     * @var bool
     */
    protected $det_use_unit_price;

    /**
     * Used in comps to store the per improvement delta to subject
     * @var int
     */
    protected $adjustmentDelta;

    /**
     * Unique Id of improvement detailed
     * @var string
     */
    protected $imprv_det_id;

    /**
     * @return int
     */
    public function getImprvDetTypeCd()
    {
        return $this->imprv_det_type_cd;
    }

    /**
     * @param int $imprv_det_type_cd
     */
    public function setImprvDetTypeCd($imprv_det_type_cd)
    {
        $this->imprv_det_type_cd = trim($imprv_det_type_cd);
    }

    /**
     * @return string
     */
    public function getImprvDetTypeDesc()
    {
        return $this->imprv_det_type_desc;
    }

    /**
     * @param string $imprv_det_type_desc
     */
    public function setImprvDetTypeDesc($imprv_det_type_desc)
    {
        $this->imprv_det_type_desc = $imprv_det_type_desc;
    }

    /**
     * @return string
     */
    public function getImprvId()
    {
        return $this->imprv_id;
    }

    /**
     * @param string $imprv_id
     */
    public function setImprvId($imprv_id)
    {
        $this->imprv_id = $imprv_id;
    }

    /**
     * @return int
     */
    public function getImprvVal()
    {
        return $this->imprv_val;
    }

    /**
     * @param int $imprv_val
     */
    public function setImprvVal($imprv_val)
    {
        $this->imprv_val = $imprv_val;
    }
    
    

    /**
     * @return int
     */
    public function getDetArea()
    {
        return $this->det_area;
    }

    /**
     * @param int $det_Area
     */
    public function setDetArea($det_Area)
    {
        $this->det_area = $det_Area;
    }

    /**
     * @return float
     */
    public function getDetUnitprice()
    {
        return $this->det_unitprice;
    }

    /**
     * @param float $det_unitprice
     */
    public function setDetUnitprice($det_unitprice)
    {
        $this->det_unitprice = $det_unitprice;
    }

    /**
     * @return boolean
     */
    public function isDetUseUnitPrice()
    {
        return $this->det_use_unit_price;
    }

    /**
     * @param boolean $det_use_unit_price
     */
    public function setDetUseUnitPrice($det_use_unit_price)
    {
        $this->det_use_unit_price = $det_use_unit_price;
    }

    /**
     * @return int mixed
     */
    public function getAdjustmentDelta()
    {
        return $this->adjustmentDelta;
    }

    /**
     * @param int $adjustmentDelta
     */
    public function setAdjustmentDelta($adjustmentDelta)
    {
        $this->adjustmentDelta = $adjustmentDelta;
    }

    /**
     * @return string
     */
    public function getImprvDetId()
    {
        return $this->imprv_det_id;
    }

    /**
     * @param string $imprv_det_id
     */
    public function setImprvDetId($imprv_det_id)
    {
        $this->imprv_det_id = $imprv_det_id;
    }

    /**
     * @return string
     */
    public function getDisplay(){
        $dispArea = ($this->getDetArea() == NULL) ? "(NONE)" : $this->getDetArea();
        return $this->getImprvDetTypeDesc() . "  " . $dispArea;
    }

    function __toString(){
        $class_vars = get_object_vars($this);
        $retstring = "";
        foreach ($class_vars as $name => $value) {
            $retstring = $retstring . "[$name : $value],";
        }
        return $retstring;
    }
}