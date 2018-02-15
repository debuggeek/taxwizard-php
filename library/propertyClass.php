<?php

include_once "defines.php";
include_once "ImpHelper.php";

class propertyClass
{

    public $mGeoID;
    public $situs_prefix;
    public $situs_number;
    public $situs_street;
    public $situs_suffix;
    public $situs_unit;
    public $situs_zip;
    public $mNeighborhood;
    public $mOwner;
    public $mHoodMIA;

    /* @var int */
    private $baseYearMktVal;

    public $mMarketVal;
    public $mSaleDate;
    public $mSaleSource;
    public $mSaleType;
    public $mImprovCount;
    /* @deprecated */
    public $mHighValImpMARCN;
    public $mPercentComp;
    /**
     * @var int
     */
    private $mLandValAdjDelta;
    public $mYearBuilt;
    public $effectiveYearBuilt;
    /**
     * @var int
     */
    private $goodAdj;
    /**
     * @var int
     */
    private $goodAdjDelta;
    /**
     * @var int
     */
    private $lASizeAdjDelta;
    public $mHVImpSqftDiff;
    public $mHVIMARCNarea;
    /**
     * @var int
     */
    private $mktLevelerDetailAdj;
    /**
     * @var int
     */
    private $mktLevelerDetailAdjDelta;
    public $mSegAdj;
    public $mSegAdjDelta;
    public $mNetAdj;
    public $mMeanVal;
    public $mMedianVal;
    public $mAgent;
    public $stateCode;
    private $mSubj;
    private $propId;
    private $mLivingArea;
    private $mSalePrice;
    /**
     * @var int
     */
    private $indicatedVal;
    /**
     * @var int
     */
    private $landValAdj;
    /**
     * @var int
     */
    private $mLASizeAdj;
    private $classCode;
    private $subClass;
    private $classAdj;
    /**
     * @var int
     */
    private $classAdjDelta;
    private $condition;
    private $mHVIMARCNareaPerSqft;
    /**
     * @var float
     */
    private $unitPrice;

    private $mPrimeImpId;

    private $tcadScore;

    //Added in 2016 to list Improvemnts
    /**
     * @var ImprovementDetailClass()
     */
    private $mImpDets;

    // The constructor, duh!
    function __construct($propid = NULL)
    {
        $this->propId = $propid;
        $this->mHoodMIA = NULL;
        $this->mImpDets = Array();
    }

    /**
     * @param bool $mSubj
     */
    public function setisSubj($isSubj)
    {
        $this->mSubj = $isSubj;
    }

    /**
     * @return null
     */
    public function getPropID()
    {
        return $this->propId;
    }

    /**
     * @param null $propId
     */
    public function setPropID($PropID)
    {
        $this->propId = $PropID;
    }

    /**
     * @param int $mLivingArea
     */
    public function setLivingArea($mLivingArea)
    {
        $this->mLivingArea = $mLivingArea;
    }

    /**
     * @param int $mSalePrice
     */
    public function setSalePrice($mSalePrice)
    {
        $this->mSalePrice = $mSalePrice;
    }

    /**
     * @param int $mLandValAdj
     */
    public function setLandValAdj($LandValAdj)
    {
        $this->landValAdj = $LandValAdj;
    }

    /**
     * @return int
     */
    function getLandValAdj()
    {
        return $this->landValAdj;
    }

    /**
     * @param propertyClass $subj
     */
    function setLandValAdjDelta($subj)
    {
        $this->mLandValAdjDelta = $subj->getLandValAdj() - $this->getLandValAdj();
    }

    /**
     * @return int
     */
    public function getLASizeAdj()
    {
        return $this->mLASizeAdj;
    }

    /**
     * @param int $mLASizeAdj
     */
    public function setLASizeAdj(int $mLASizeAdj)
    {
        $this->mLASizeAdj = $mLASizeAdj;
    }

    /**
     * @return int
     */
    public function getBaseYearMktVal(): int
    {
        return $this->baseYearMktVal;
    }

    /**
     * @param int $baseYearMktVal
     */
    public function setBaseYearMktVal(int $baseYearMktVal)
    {
        $this->baseYearMktVal = $baseYearMktVal;
    }


    /**
     * @param mixed $mMarketVal
     */
    public function setMarketVal($mMarketVal)
    {
        $this->mMarketVal = $mMarketVal;
    }

    /**
     * @param array $mImpDets
     */
    public function setImpDets($mImpDets)
    {
        $this->mImpDets = $mImpDets;
    }

    /**
     * @return mixed
     */
    public function getImprovCount()
    {
        return $this->mImprovCount;
    }

    /**
     * @param mixed $mImprovCount
     */
    public function setImprovCount($ImprovCount)
    {
        $this->mImprovCount = $ImprovCount;
    }

    /**
     * @return mixed
     */
    public function getPrimeImpId()
    {
        return $this->mPrimeImpId;
    }

    /**
     * @param mixed $mPrimeImpId
     */
    public function setPrimeImpId($PrimeImpId)
    {
        $this->mPrimeImpId = $PrimeImpId;
    }

    function __toString()
    {
        return json_encode($this);
    }

    function getNMIA()
    {
        global $NEIGHBMIA, $NEIGHB, $PROPID;
        $hoodval = $this->mNeighborhood;
        $prop_id = $this->propId;

        if ($this->mHoodMIA != NULL)
            return $this->mHoodMIA;

        $query = "SELECT * FROM " . $NEIGHBMIA["TABLE"] . " WHERE prop_id=$prop_id";
        //echo "getNMIA::query = " . $query . "<br>";
        $result = doSqlQuery($query);
        $num = mysqli_num_rows($result);

        if ($num == 0)
            return "No Value Found!";

        $row = mysqli_fetch_array($result);
        $adjarray = explode(";", $row[$NEIGHBMIA["FIELD"]], 100);
        if (count($adjarray) == 0)
            return "No Value Found!";

        foreach ($adjarray as $entry) {
            $entryarray = explode(":", $entry, 2);
            if (count($entryarray) != 0) {
                if (strcmp(trim($hoodval), trim($entryarray[0])) == 0) {
                    $this->mHoodMIA = $entryarray[1];
                    return $this->mHoodMIA;
                }
            }
        }

        return "No Value Found!";
    }

    /**
     * @return mixed
     */
    public function getMarketVal()
    {
        return $this->mMarketVal;
    }

    /**
     * @return int
     */
    public function getSalePrice()
    {
        return $this->mSalePrice;
    }

    /**
     * @return \TaxWizard\TcadScore
     */
    public function getTcadScore()
    {
        return $this->tcadScore;
    }

    /**
     * @param mixed $tcadScore
     */
    public function setTcadScore($tcadScore)
    {
        $this->tcadScore = $tcadScore;
    }

    /**
     * Represents percentage as int value
     * @return int
     */
    function getGoodAdj(){
        return $this->goodAdj;
    }

    /**
     * Represents percentage as int value
     * @param int $goodAdj
     */
    public function setGoodAdj(int $goodAdj)
    {
        $this->goodAdj = $goodAdj;
    }

    function getSecondaryImp()
    {
        // Will return the sum of non-primeid values of improv_val in Special_IMP
        if ($this->getImpCount() == 1) {
            return 0;
        }

        $value = 0;


        return $value;
    }

    function getImpDetCount()
    {
        return count($this->mImpDets);
    }

    /**
     * @return int
     */
    public function getLASizeAdjDelta(): int
    {
        return $this->lASizeAdjDelta;
    }

    /**
     * @param propertyClass $subjdetailadj
     * @return int|void
     */
    function setLASizeAdjDelta($subjdetailadj)
    {
        global $debug;
        if ($this->mSubj == true) { return 0; }

        if ($this->lASizeAdjDelta != null) {
            error_log("setLASizeAdjDelta> attempting to re-calculate for property");
            return;
        }

        $subjLASizeAdj = $subjdetailadj->getLASizeAdj();
        $compLASizeAdj = $this->getLASizeAdj();
        // Change in 2017 to device by unit price
        //$subjUnitPrice = number_format($subjdetailadj->getHVImpMARCNPerSQFT(), 2);
        $subjUnitPrice = $subjdetailadj->getUnitPrice();

        $this->lASizeAdjDelta = round(($subjLASizeAdj - $compLASizeAdj) * $subjUnitPrice);
        if ($debug) error_log("setLASizeAdjDelta: (" . $compLASizeAdj . "-" . $compLASizeAdj . ")*" . $subjUnitPrice . " = " . $this->lASizeAdjDelta);

    }

    function setHVImpSqftDiff($subjdetailadj)
    {
        $var1 = $subjdetailadj->mLASizeAdj;
        $var2 = $this->mLASizeAdj;

        $this->mHVImpSqftDiff = ($var1 - $var2);
    }

    function getHVImpSqftDiff($subjdetailadj)
    {
        return $this->mHVImpSqftDiff;
    }

    /**
     * @param propertyClass $subjDetailAdj
     */
    function setMktLevelerDetailAdjDelta($subjDetailAdj)
    {
//        error_log("setMktLevelerDetailAdjDelta")
        $this->mktLevelerDetailAdjDelta = $subjDetailAdj->getMktLevelerDetailAdj() - $this->getMktLevelerDetailAdj();
    }


    /**
     * @return int
     */
    function getMktLevelerDetailAdjDelta()
    {
        return $this->mktLevelerDetailAdjDelta;
    }

    /**
     * The Mkt Leveler Detail Adj is set by determine
     * @param int $mMktLevelerDetailAdj
     */
    public function setMktLevelerDetailAdj($mktLevelerDetailAdj)
    {
        $this->mktLevelerDetailAdj = $mktLevelerDetailAdj;
    }

    /**
     * @return int
     */
    function getMktLevelerDetailAdj()
    {
        return $this->mktLevelerDetailAdj;
    }

    function getYearBuilt()
    {
        return $this->mYearBuilt;
    }

    function lookupHVImpMARCN($data)
    {
        global $HIGHVALIMPMARCN, $allowablema;

        $mafield = "Imprv_det_type_cd";

        //sometimes we appear to look this up before the improvements...not sure why that's so, but this hack saves the day
        if ($data === null) {
            $this->getImpCount();
            $data = $this->mPrimeImpId;
        }
        //$query = "SELECT * FROM ".$HIGHVALIMPMARCN["TABLE"]." WHERE prop_id='$propid'";

        $subquery = "";
        $i = 0;

        while ($i < count($allowablema)) {
            $subquery .= "imprv_det_type_cd='$allowablema[$i]'";
            if (++$i < count($allowablema))
                $subquery .= " OR ";
        }

        $query = "SELECT det_calc_val FROM IMP_DET, SPECIAL_IMP
		WHERE IMP_DET.prop_id='$this->propId'
		AND ( " . $subquery . ")
		AND imprv_det_id = det_id
		AND IMP_DET.prop_id = SPECIAL_IMP.prop_id";

        $query .= " AND IMP_DET.imprv_id = '$data'";
        //echo "$query";

        $result = doSqlQuery($query);

        if (!$result)
            return "No Value Found!";

        $value = 0;
        while ($row = mysqli_fetch_array($result)) {
            $value += $row['det_calc_val'];
        }
        return $value;
    }

    /**
     * @param mixed $mSegAdj
     */
    public function setSegAdj($SegAdj)
    {
//        error_log("setSegAdj:" . $SegAdj);
        $this->mSegAdj = $SegAdj;
    }

    public function getSegAdj()
    {
        return $this->mSegAdj;
    }

    function setSegAdjDelta($subj)
    {
        $this->mSegAdjDelta = $subj->getSegAdj() - $this->getSegAdj();
    }

    /**
     * @return mixed
     */
    public function getSitus()
    {
        return implode(' ', [$this->situs_prefix,
                                $this->situs_number,
                                $this->situs_street,
                                $this->situs_suffix,
                                $this->situs_unit,
                                $this->situs_zip]);
    }


    function setMeanVal($numComps)
    {
        if ($this->mSubj == false)
            return NULL;
        $result = 0;
        for ($j = 1; $j <= $numComps; $j++) {
            $data = $_SESSION["comp" . $j];
            $result = $result + $data->getIndicatedVal();
        }
        $this->mMeanVal = (int)($result / $numComps);
    }

    function setMedianVal($numComps)
    {
        if ($this->mSubj == false)
            return null;
        $median = -1;
        $comparray = array();
        for ($i = 1; $i <= $numComps; $i++) {
            $data = $_SESSION["comp" . $i];
            $comparray[$i - 1] = $data->getIndicatedVal();
        }
        sort($comparray);
        //var_dump($comparray);
        if ($numComps % 2) {
            $median = $comparray[floor($numComps / 2)];
        } else {
            $median = ($comparray[$numComps / 2] + $comparray[($numComps / 2) - 1]) / 2;
        }
        $this->mMedianVal = $median;
    }

    function getAgent()
    {
        return $this->mAgent;
    }

    /**
     * @deprecated
     * @param $fieldConst
     * @param $value
     */
    function setField($fieldConst, $value)
    {
        /*
         * NOTE
         * Moving away from setting via field name and instead through setter
         */
        global $PROPID, $GEOID, $SITUS, $NEIGHB, $OWNER, $NEIGHBMIA, $MARKETVALUE, $LIVINGAREA, $SALEDATE, $SALEPRICE, $SALESOURCE, $SALETYPE,
               $IMPROVEMENTCNT, $HIGHVALIMPMARCN, $COMPLETE, $LANDVALUEADJ, $CLASSADJ, $ACTUALYEARBUILT,
               $GOODADJ, $LASIZEADJ, $MKTLEVELERDETAILADJ, $SEGMENTSADJ;

        error_log("WARNING still using setField for setting field " .$fieldConst." to ".$value."<br>");
        if ($fieldConst === NULL)
            return;

        if (is_string($value)) {
            $value = trim($value);
        }
        switch ($fieldConst) {
            case($PROPID["NAME"]):
                $this->propId = $value;
                break;
            case($GEOID["NAME"]):
                $this->mGeoID = $value;
                break;
            case($SITUS["NAME"]):
                $this->mSitus = $value;
                break;
            case($NEIGHB["NAME"]):
                $this->mNeighborhood = $value;
                break;
            case($NEIGHBMIA["NAME"]):
                $this->mHoodMIA = $value;
                break;
            case($OWNER["NAME"]):
                $this->mOwner = $value;
                break;
            case($MARKETVALUE["NAME"]):
                $this->mMarketVal = $value;
                break;
            case($LIVINGAREA["NAME"]):
                $this->mLivingArea = $value;
                break;
            case($SALEDATE["NAME"]):
                $this->mSaleDate = $value;
                break;
            case($SALEPRICE["NAME"]):
                $this->mSalePrice = $value;
                break;
            case($SALESOURCE["NAME"]):
                $this->mSaleSource = $value;
                break;
            case($SALETYPE["NAME"]):
                $this->mSaleType = $value;
                break;
            case($IMPROVEMENTCNT["NAME"]):
                $this->mImprovCount = $value;
                break;
            case($HIGHVALIMPMARCN["NAME"]):
                $this->mHighValImpMARCN = $value;
                break;
            case($COMPLETE["NAME"]):
                $this->mPercentComp = $value;
                break;
            case($LANDVALUEADJ["NAME"]):
                $this->setLandValAdj($value);
                break;
            case($CLASSADJ["NAME"]):
                $this->classAdj = $value[0].$value[1];
                break;
            case($ACTUALYEARBUILT["NAME"]):
                $this->mYearBuilt = $value;
                break;
            case($GOODADJ["NAME"]):
                $this->setGoodAdj($value);
                break;
            case($LASIZEADJ["NAME"]):
                $this->setLASizeAdj($value);
                break;
//            case($MKTLEVELERDETAILADJ["NAME"]):
//                $this->mMktLevelerDetailAdj = $value;
//                break;
            case($SEGMENTSADJ["NAME"]):
                $this->mSegAdj = $value;
            default:
                error_log("ERROR: UNKNOWN field on set: " . $fieldConst);
        }

    }

    /**
     * @param string $field
     * @return float|null|string
     */
    function getFieldByName($field)
    {
        global $PROPID, $GEOID, $SITUS, $NEIGHB, $OWNER, $NEIGHBMIA,
               $BASEYEARMKTVAL, $MARKETVALUE, $MARKETPRICESQFT, $BASECURRDIFF,
               $LIVINGAREA, $SALEDATE, $SALEPRICE, $SALEPRICESQFT, $SALERATIO, $SALETYPEANDCONF, $SALETYPE, $ADJSALEPRICE,
               $IMPROVEMENTCNT, $HIGHVALIMPMARCN, $HIGHVALIMPMARCNSQFT, $COMPLETE,
               $STATECODE,
               $LANDVALUEADJ, $CLASSADJ, $ACTUALYEARBUILT, $GOODADJ, $LASIZEADJ, $HIGHVALIMPMASQFTDIFF, $MKTLEVELERDETAILADJ,
               $SEGMENTSADJ, $SEGMENTSADJSIMPLE,
               $NETADJ, $INDICATEDVAL, $INDICATEDVALSQFT, $MEANVAL, $MEANVALSQFT, $MEDIANVAL, $MEDIANVALSQFT,
               $TCADSCORE;

        global $landvaladjdelta, $classadjdelta, $goodadjdelta, $lasizeadjdelta, $mktlevelerdetailadjdelta,
               $segmentsadjdelta, $segmentsadjMultiRow;

        if ($field === NULL){
            //gets called for every empty field in presentation
            //error_log("getFieldByName: null field passed in");
            return;
        }

        switch ($field) {
            case($PROPID["NAME"]):
                return $this->propId;
            case($GEOID["NAME"]):
                return $this->mGeoID;
            case($SITUS["NAME"]):
                return $this->getSitus();
            case($NEIGHB["NAME"]):
                return $this->mNeighborhood;
            case($NEIGHBMIA["NAME"]):
                return "0.00%";//$this->mHoodMIA;
            case($OWNER["NAME"]):
                return $this->mOwner;
            case($BASEYEARMKTVAL["NAME"]):
                return number_format($this->getBaseYearMktVal());
            case($MARKETVALUE["NAME"]):
                return number_format($this->mMarketVal);
            case($BASECURRDIFF["NAME"]):
                return (number_format($this->getBaseCurrDiff(), 4) * 100) . "%";
            case($MARKETPRICESQFT["NAME"]):
                return number_format($this->getMrktSqft(), 2);
            case($LIVINGAREA["NAME"]):
                return $this->getLivingArea();
            case($SALEDATE["NAME"]):
                if (isNotMLS($this))
                    return $this->mSaleDate;
                else
                    return $this->mSaleDate . "_";
            case($SALEPRICE["NAME"]):
                if (isFlaggableSaleType($this))
                    return $this->mSalePrice . ".";
                else
                    return $this->mSalePrice;
            case($ADJSALEPRICE["NAME"]):
                return $this->getAdjSalePrice();
            case($SALEPRICESQFT["NAME"]):
                return number_format($this->getSalePerSQFT());
            case($SALERATIO["NAME"]):
                return number_format($this->getSaleRatio(), 4);
            case($SALETYPE["NAME"]):
                return $this->getSaleType();
            case($SALETYPEANDCONF["NAME"]):
                return $this->getSaleTypeAndConf();
            case($IMPROVEMENTCNT["NAME"]):
                return $this->mImprovCount;
            case($HIGHVALIMPMARCN["NAME"]):
                return number_format($this->mHighValImpMARCN);
            case($HIGHVALIMPMARCNSQFT["NAME"]):
                return number_format($this->getHVImpMARCNPerSQFT(), 2);
            case($COMPLETE["NAME"]):
                return $this->mPercentComp;
            case($STATECODE["NAME"]):
                return $this->stateCode;
            case($LANDVALUEADJ["NAME"]):
                return $this->getLandValAdj();
            case($landvaladjdelta):
                return number_format($this->getLandValueAdjDelta());
            case($CLASSADJ["NAME"]):
                return $this->getClassAdj();
            case($classadjdelta):
                return number_format($this->getClassAdjDelta());
            case($ACTUALYEARBUILT["NAME"]):
                return $this->mYearBuilt;
            case($GOODADJ["NAME"]):
                if ($this->mSubj) {
                    $year = date("Y");
                    if ($this->mYearBuilt <= ($year - 25)) {
                        if ($this->getGoodAdj() >= 75) {
                            return $this->getGoodAdj() . "_";
                        }
                    }
                }
                return $this->getGoodAdj();
            case($goodadjdelta):
                return number_format($this->getGoodAdjDelta());
            case($LASIZEADJ["NAME"]):
                return $this->getLASizeAdj();
            case($lasizeadjdelta):
                return number_format($this->getLASizeAdjDelta());
            case($HIGHVALIMPMASQFTDIFF["NAME"]):
                return $this->mHVImpSqftDiff;
            case($MKTLEVELERDETAILADJ["NAME"]):
                return  $this->getMktLevelerDetailAdj();
            case($mktlevelerdetailadjdelta):
                return number_format($this->getMktLevelerDetailAdjDelta());
            case($SEGMENTSADJ["NAME"]):
                return $this->mSegAdj;
            case($SEGMENTSADJSIMPLE["NAME"]):
                return (string)$this->getSegAdj();
            case($segmentsadjMultiRow):
                return $this->getImpDets();
            case($NETADJ["NAME"]):
                return number_format($this->mNetAdj);
            case($INDICATEDVAL["NAME"]):
                return $this->getIndicatedVal(true);
            case($INDICATEDVALSQFT["NAME"]):
                return number_format($this->getIndicatedValSqft(), 2);
            case($MEANVAL["NAME"]):
                return number_format($this->mMeanVal);
            case($MEANVALSQFT["NAME"]):
                return number_format($this->getMeanValSqft(), 2);
            case($MEDIANVAL["NAME"]):
                return number_format($this->mMedianVal);
            case($MEDIANVALSQFT["NAME"]):
                return number_format($this->getMedianValSqft(), 2);
            case($segmentsadjdelta):
                return number_format($this->mSegAdjDelta);
            case($TCADSCORE["NAME"]):
                $tcadScore = $this->getTcadScore();
                return $tcadScore === null ? null : $tcadScore->getScore();
            default:
                return "ERROR: UNKNOWN field on get:" . $field . PHP_EOL;
        }
    }

    function getMrktSqft()
    {
        if ($this->mLivingArea == 0) {
            return null;
        }
        return $this->mMarketVal / $this->mLivingArea;
    }

    function getSalePerSQFT()
    {
        if ($this->mLivingArea == 0) {
            return null;
        }
        return $this->mSalePrice / $this->mLivingArea;
    }

    /**
     * @return float
     */
    function getSaleRatio(){
        if($this->getSalePrice() > 0) {
            return $this->getMarketVal() / $this->getSalePrice();
        }
    }

    function getSaleTypeAndConf(){
        return $this->getSaleType();
    }

    /**
     * Should return the sale price minus any adjustments
     *
     * Per George, a “Sale Adjustment Amount” would be extremely rare.  This would entail personal property that
     * was included within a sale.  For example, George mentioned there was a $1+M condo that included a $40K boat
     * with the purchase.  In this instance, the $40k would be subtracted from the property’s sale
     * price to arrive at “Adjusted Sale Price”.
     *
     * @return float
     */
    function getAdjSalePrice(){
        //todo need column/sample of adj amount
        return $this->getSalePrice();
    }
    /**
     * @return array
     */
    function getClassAdj()
    {
        return $this->getClassCode().$this->getSubClass();
    }

    /**
     * @return mixed
     */
    public function getClassCode()
    {
        return $this->classCode;
    }

    /**
     * @param mixed $classCode
     */
    public function setClassCode($classCode)
    {
        $this->classCode = $classCode;
    }

    /**
     * @return string
     */
    public function getSubClass()
    {
        return $this->subClass;
    }

    /**
     * @param string $subClass
     */
    public function setSubClass($subClass)
    {
        $this->subClass = $subClass;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * @param bool $equityComp
     * @return mixed
     */
    function calcIndicatedVal($equityComp){
        if ($this->mSubj == true) {
            return $this->getMarketVal();
        }
        $var = null;
        if ($equityComp) {
            $var1 = $this->getMarketVal();
        } else {
            $var1 = $this->getSalePrice();
        }
        $result = $var1 + $this->getNetAdj();
        return $result;
    }

    function setIndicatedVal($val){
        $this->indicatedVal = $val;
    }

    /**
     * @param bool
     * @return int|string
     */
    function getIndicatedVal($pretty){
        if($pretty) {
            return number_format($this->indicatedVal);
        } else {
            return $this->indicatedVal;
        }
    }

    /**
     * @return int
     * @throws Exception
     */
    function calcNetAdj() : int{
        if ($this->isSubj() == true) {
            //Don't adjust a subject
            return 0;
        }

        $landValueAdjDelta = $this->getLandValueAdjDelta();
        $classAdjDelta = $this->getClassAdjDelta();
        //No longer in sheet as of 2015 and back as of 2017
        $LASizeAdjDelta = $this->getLASizeAdjDelta();
        $goodAdjDelta = $this->getGoodAdjDelta();
        //No longer in sheet as of 2015 and back as of 2017
        $mktLevelerDetailAdjDelta = $this->getMktLevelerDetailAdjDelta();
        //No longer in 2017
        //$impDetSegAdj = $this->getImpDetSegAdj();
        $segAdjDelta = $this->getSegAdjDelta();

        $result = $landValueAdjDelta + $classAdjDelta + $LASizeAdjDelta + $goodAdjDelta + $mktLevelerDetailAdjDelta + $segAdjDelta;
        if(is_nan($result)){
            error_log("calcNetadj for propId=".$this->propId." has bad values to calculate with");
            throw new Exception("calcNetAdj resulted in NAN");
        }
        return $result;
    }

    /**
     * @param $netAdj
     */
    function setNetAdj($netAdj){
        $this->mNetAdj = $netAdj;
    }

    /**
     * @return int
     */
    function getNetAdj()
    {
        return $this->mNetAdj;
    }

    /**
     * @return bool
     */
    public function isSubj()
    {
        return $this->mSubj;
    }

    /**
     * @return int
     */
    function getLandValueAdjDelta()
    {
        return $this->mLandValAdjDelta;
    }

    /**
     * @return int
     */
    function getClassAdjDelta()
    {
        return $this->classAdjDelta;
    }

    /**
     * @param propertyClass $subjdetailadj
     */
    function setClassAdjDelta($subjdetailadj)
    {
        global $debugquery;

        $option = 2017;
        if ($this->classAdjDelta != null){
            error_log("setClassAdjDelta: Attempted more then once, already set");
            return;
        }

        if ($option == 2017) {
            // ((Subj unit price /Comp unit price) -1 ) * Comp main area RCN
            // Basicaly back to 2010 :)
            $var1 = $subjdetailadj->getUnitPrice();
            $var2 = $this->getUnitPrice();
            $var3 = $var1 / $var2;
            $var4 = $var3 - 1;
            $result = $var4 * $var2 * $this->mLivingArea;
            $this->classAdjDelta = round($result);
        } else if ($option == 2016) {
            // Added for 2016
            // According to TCAD : Comp Mkt Value * ( Subj Most Valuable Improvement unit - Comp Most Valuable Improvement unit)
            $subjMostValuableImpUnit = $subjdetailadj->getMostValueImpUnit()->getDetUnitprice();
            $compMostValuebleImpUnit = $this->getMostValueImpUnit()->getDetUnitprice();
            $result = $this->mLivingArea * ($subjMostValuableImpUnit - $compMostValuebleImpUnit);
            $this->classAdjDelta = round($result);
        } else if ($option == 2015) {
            //This was the 09 Formula Variables that we returned to in '14
            $var1 = number_format($subjdetailadj->getHVImpMARCNPerSQFT(), 2);
            $var2 = number_format($this->getHVImpMARCNPerSQFT(), 2);
            if ($var2 == 0) {
                //happens when property is missing improvements
                $this->classAdjDelta = 0;
                return;
            }
            $var3 = $var1 / $var2;
            $var4 = $var3 - 1;
            $result = $var4 * $this->mHighValImpMARCN;
            if ($debugquery) error_log("setClassAdjDelta[" . $this->propId . "]: ((" . $var1 . "/" . $var2 . ") - 1) *" . $this->mHighValImpMARCN . " = " . $result);
            $this->classAdjDelta = $result;
        } else {
            //2010~ version of class adj
            $var1 = $subjdetailadj->getUnitPrice();
            $var2 = $this->getUnitPrice();
            $var3 = $var1 / $var2;
            $var4 = $var3 - 1;
            $result = $var4 * $var2 * $this->mLivingArea;
            $this->classAdjDelta = $result;
        }
    }

    /**
     * @return ImprovementDetailClass
     */
    public function getMostValueImpUnit()
    {
        foreach ($this->getImpDets() as $impDet) {
            /* @var $impDet ImprovementDetailClass */
            if ($impDet->isDetUseUnitPrice()) {
                return $impDet;
            }
        }
        error_log("Unable to find most valuable improvement for " . $this->propId);
        return null;
    }

    /**
     * @return array
     */
    public function getImpDets()
    {
        return $this->mImpDets;
    }

    function getHVImpMARCNPerSQFT()
    {
        if ($this->mLivingArea == 0) {
            return null;
        }
        if ($this->mHVIMARCNareaPerSqft === null) {
            if ($this->mImprovCount == 1) {
                $this->mHVIMARCNareaPerSqft = $this->getHVImpMARCN() / $this->mLivingArea;
            } else {
                if ($this->mHVIMARCNarea === null) {
                    $this->mHVIMARCNarea = $this->HighValFieldLookup('imprv_det_area', $this->mPrimeImpId);
                    if ($this->mHVIMARCNarea === null) {
                        error_log("ERROR> Unable to find improvements for " . $this->propId);
                        return null;
                    }
                }
                $this->mHVIMARCNareaPerSqft = $this->getHVImpMARCN() / $this->mHVIMARCNarea;
            }
        }
        return $this->mHVIMARCNareaPerSqft;
    }

    function getHVImpMARCN()

    {
        if ($this->mHighValImpMARCN === null) {
            $this->mHighValImpMARCN = $this->HighValFieldLookup('det_calc_val', $this->mPrimeImpId);
        }
        return $this->mHighValImpMARCN;
    }

    function HighValFieldLookup($field, $data)
    {
        global $HIGHVALIMPMARCN, $allowablema;
        $mafield = "Imprv_det_type_cd";
        //sometimes we appear to look this up before the improvements...not sure why that's so, but this hack saves the day
        if ($data === null) {
            $this->getImpCount();
            $data = $this->mPrimeImpId;
        }
        //$query = "SELECT * FROM ".$HIGHVALIMPMARCN["TABLE"]." WHERE prop_id='$propid'";
        $subquery = "";
        $i = 0;
        while ($i < count($allowablema)) {
            $subquery .= "imprv_det_type_cd='$allowablema[$i]'";
            if (++$i < count($allowablema))
                $subquery .= " OR ";
        }
        $query = "SELECT $field FROM IMP_DET, SPECIAL_IMP
		WHERE IMP_DET.prop_id='$this->propId'
		AND ( " . $subquery . ")
		AND imprv_det_id = det_id
		AND IMP_DET.prop_id = SPECIAL_IMP.prop_id";

        $query .= " AND IMP_DET.imprv_id = '$data'";
        //echo "$query";
        $result = doSqlQuery($query);
        if (!$result) {
            return "No Value Found!";
        }
        $value = 0;
        while ($row = mysqli_fetch_array($result)) {
            $value += $row[$field];
        }
        return $value;
    }

    function getImpCount()
    {

        if ($this->mImprovCount != null)
            return $this->mImprovCount;

        $this->mImprovCount = count(ImpHelper::getUniqueImpIds($this->getImpDets()));
        $this->mPrimeImpId = ImpHelper::getPrimaryImpId($this->getImpDets());

        return $this->mImprovCount;
    }

    /**
     * @return float
     */
    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    /**
     * @param float $unitPrice
     */
    public function setUnitPrice(float $unitPrice)
    {
        if($unitPrice == null || $unitPrice == 0) {
            error_log("ERROR: unit price for ".$this->propId." being set to ".$unitPrice);
        }
        $this->unitPrice = $unitPrice;
    }

    /**
     * @return int
     */
    function getGoodAdjDelta()
    {
        return $this->goodAdjDelta;
    }

    /**
     * @param propertyClass $subj
     * @param $isEquity
     * @throws Exception
     * @internal param propertyClass $subjdetailadj
     */
    function setGoodAdjDelta($subj, $isEquity)
    {
        $option = 2017;

        $compPrice = 0;
        if ($isEquity) {
            $compPrice = $this->getMarketVal();
        }
        else {
            $compPrice = $this->getSalePrice();
        }

        $result = 0;

        if ($option == 2017) {
            // ( Comp Price - Comp Land Value - Comp Secondary Value ) * (Subj % good - Comp % good)
            $compLV = $this->getLandValAdj();
            $compSecVal = $this->getSegAdj();

            $subjGood = $subj->getGoodAdj();
            $compGood = $this->getGoodAdj();
            $goodCalc = ($subjGood - $compGood) / 100; //treat % good as percentage
            $result = ($compPrice - $compLV - $compSecVal) * $goodCalc;
        } else if ($option == 2016) {
            // (Comp Sale price - Comp Land Value) * (Subj % good - Comp % good)
            $var2 = $this->getLandValAdj();
            $result = ($compPrice - $var2) * ($subj->getGoodAdj() - $this->getGoodAdj()) / 100;
        }  else {
            throw new Exception("setGoodAdjDelta>> Must provide configuration option");
        }

        $this->goodAdjDelta = $result;
    }


    /**
     * Gets the sum of deltas on the primary and secondary improvments
     * @return int
     */
    public function getImpDetSegAdj()
    {
        $sum = ImpHelper::calcDeltaSum($this->getImpDets());
        $sum += $this->getSegAdjDelta();
        return $sum;
    }

    /**
     * @return mixed
     */
    function getSegAdjDelta()
    {
        return $this->mSegAdjDelta;
    }

    function getIndicatedValSqft()
    {
        if ($this->isSubj() == true)
            return NULL;
        if ($this->getLivingArea() == 0)
            return NULL;
        return ($this->getIndicatedVal(false) / $this->getLivingArea());
    }

    /**
     * @return int
     */
    public function getLivingArea()
    {
        return $this->mLivingArea;
    }

    function getMeanValSqft()
    {
        if ($this->mSubj == false)
            return null;
        return $this->mMeanVal / $this->mLivingArea;
    }

    function getMedianValSqft()
    {
        if ($this->mSubj == false)
            return null;

        return $this->mMedianVal / $this->mLivingArea;
    }

    function emitXML()
    {
        echo nl2br('<PROPID>' . $this->propId . '</PROPID>');
        //echo nl2br()
    }

    /**
     * @param mixed $mSaleSource
     */
    public function setSaleSource($mSaleSource)
    {
        $this->mSaleSource = $mSaleSource;
    }

    /**
     * @return mixed
     */
    public function getSaleSource()
    {
        return $this->mSaleSource;
    }

    /**
     * @return mixed
     */
    public function getSaleType()
    {
        return $this->mSaleType;
    }

    /**
     * @param mixed $mSaleType
     */
    public function setSaleType($mSaleType)
    {
        $this->mSaleType = $mSaleType;
    }

    public function toJson()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    private function getBaseCurrDiff()
    {
        return ($this->getMarketVal() - $this->getBaseYearMktVal()) / $this->getMarketVal();
    }

} // end of class
