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
    public $mMarketVal;
    public $mSaleDate;
    public $mSaleSource;
    public $mSaleType;
    public $mImprovCount;
    /* @deprecated */
    public $mHighValImpMARCN;
    public $mPercentComp;
    public $mLandValAdjDelta;
    public $mYearBuilt;
    public $effectiveYearBuilt;
    public $mGoodAdj;
    public $mGoodAdjDelta;
//    public $mLASizeAdj;
    public $mLASizeAdjDelta;
    public $mHVImpSqftDiff;
    public $mHVIMARCNarea;
    public $mMktLevelerDetailAdj;
    public $mMktLevelerDetailAdjDelta;
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
    private $mIndVal;
    /**
     * @var int
     */
    private $mLandValAdj;
    private $mLASizeAdj;
    private $classCode;
    private $subClass;
    private $classAdj;
    private $classAdjDelta;
    private $condition;
    private $mHVIMARCNareaPerSqft;
    private $mUnitPrice;

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
        $this->mLandValAdj = (int)$LandValAdj;
    }

    /**
     * @return int
     */
    function getLandValAdj()
    {

        if ($this->mLandValAdj != null) {
            return $this->mLandValAdj;
        } else {
            error_log("Land Val Adjustment NOT set yet");
        }
    }

    /**
     * @param propertyClass $subj
     */
    function setLandValAdjDelta($subj)
    {
        $this->mLandValAdjDelta = $subj->getLandValAdj() - $this->getLandValAdj();
    }

    /**
     * @return mixed
     */
    public function getLASizeAdj()
    {
        return $this->mLASizeAdj;
    }

    /**
     * @param mixed $mLASizeAdj
     */
    public function setLASizeAdj($mLASizeAdj)
    {
        $this->mLASizeAdj = $mLASizeAdj;
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
     * @param propertyClass $subjdetailadj
     */
    function setGoodAdjDelta($subj, $isEquity)
    {
        $option = 2016;

        if ($isEquity)
            $var1 = $this->getMarketVal();
        else
            $var1 = $this->getSalePrice();

        if ($option == 2016) {
            // (Comp Sale price - Comp Land Value) * (Subj % good - Comp % good)
            $var2 = $this->getLandValAdj();
            $this->mGoodAdjDelta = ($var1 - $var2) * ($subj->getGoodAdj() - $this->getGoodAdj()) / 100;
        } else {
            $var2 = $this->mLandValAdj;
            $var3 = $this->getSegAdj();

            if ($subj->mGoodAdj === null)
                $subj->mGoodAdj = $subj->getGoodAdj();
            $var4 = $subj->mGoodAdj;

            if ($this->mGoodAdj === null)
                $this->mGoodAdj = $this->getGoodAdj();
            $var5 = $this->mGoodAdj;

            $this->mGoodAdjDelta = ($var1 - $var2 - $var3) / 100 * ($var4 - $var5);
        }
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

    function getGoodAdj()
    {
        if(empty($this->mGoodAdj)){
            throw new Exception("mGoodAdj not set as expected");
        }
        return $this->mGoodAdj;
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

    function setLASizeAdjDelta($subjdetailadj)
    {
        global $debug;
        $debug=true;

        if ($this->mSubj == true)
            return NULL;
        if ($this->mLASizeAdjDelta != null)
            return;
        $var1 = $subjdetailadj->mLASizeAdj;
        if ($this->mLASizeAdj === null)
            $this->mLASizeAdj = $this->getLASizeAdj();
        $var2 = $this->mLASizeAdj;
        $var3 = number_format($subjdetailadj->getHVImpMARCNPerSQFT(), 2);

        $this->mLASizeAdjDelta = ($var1 - $var2) * $var3;
        if ($debug) error_log("getLASizeAdjDelta: (" . $var1 . "-" . $var2 . ")*" . $var3 . " = " . $this->mLASizeAdjDelta);
        //Now that we have a LASizeAdjDelta we can also compute the HVIMASQFTDIFF
        $this->setHVImpSqftDiff($subjdetailadj);
        $debug=false;
    }

//    function getLASizeAdj()
//    {
//        global $allowablema, $debugquery;
//
//        if ($this->mLASizeAdj != null)
//            return $this->mLASizeAdj;
//
//        $propid = $this->propId;
//        $subquery = "";
//
//        $i = 0;
//        while ($i < count($allowablema)) {
//            $subquery .= "imprv_det_type_cd='$allowablema[$i]'";
//            if (++$i < count($allowablema))
//                $subquery .= " OR ";
//        }
//
//        $query = "SELECT det_area FROM IMP_DET, SPECIAL_IMP
//				WHERE IMP_DET.prop_id='$propid'
//				AND ( " . $subquery . ")
//				AND imprv_det_id = det_id
//				AND IMP_DET.prop_id = SPECIAL_IMP.prop_id
//				AND IMP_DET.imprv_id = '$this->mPrimeImpId'";
//
//        if ($debugquery) error_log("getLASizeAdj[" . $this->propId . "]: query=" . $query);
//        $result = doSqlQuery($query);
//        $num = mysqli_num_rows($result);
//
//        if (!$result)
//            return "No Value Found!";
//
//        $value = 0;
//
//        while ($row = mysqli_fetch_array($result)) {
//            $value += $row["det_area"];
//        }
//
//        return $value;
//    }

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

    function setMktLevelerDetailAdjDelta($subjdetailadj)
    {
        $this->mMktLevelerDetailAdjDelta = (int)$subjdetailadj->mMktLevelerDetailAdj - (int)$this->getMktLevelerDetailAdj();
    }

    // Pass the the SQL field to sum over for HVMARCN data

    function getMktLevelerDetailAdj()
    {
        if ($this->mMktLevelerDetailAdj === null) {
            global $MKTLEVELERDETAILADJ, $TABLE_IMP_DET, $TABLE_SPEC_IMP, $allowablema, $mafield, $debugquery;
            $propid = $this->propId;
            $subquery = "";
            $target = "det_calc_val";
            $target2 = "det_val";

            $i = 0;
            while ($i < count($allowablema)) {
                $subquery .= "imprv_det_type_cd != '$allowablema[$i]'";
                if (++$i < count($allowablema))
                    $subquery .= " AND ";
            }

            $query = "SELECT SPECIAL_IMP.$target,SPECIAL_IMP.$target2 FROM $TABLE_SPEC_IMP,$TABLE_IMP_DET
			WHERE $TABLE_IMP_DET.prop_id = '$propid' AND $TABLE_SPEC_IMP.prop_id = '$propid'
			AND $TABLE_IMP_DET.Imprv_det_id = $TABLE_SPEC_IMP.det_id
			AND ( " . $subquery . ")";

            if ($this->getImpCount() > 1) {
                $query .= " AND IMP_DET.imprv_id = '$this->mPrimeImpId'";
            }
            if ($debugquery) error_log("getMktLevelerDetailAdj[" . $this->propId . "]: query=" . $query);
            $result = doSqlQuery($query);

            if (!$result) {
                return "No Value Found!";
            }
            $num = mysqli_num_rows($result);

            if ($num == 0) {
                return "No Value Found!";
            }
            $value = 0;

            while ($row = mysqli_fetch_array($result)) {
                if ($row[$target] == 0)
                    $value += $row[$target2];
                else
                    $value += $row[$target];
            }
            $this->mMktLevelerDetailAdj = $value;
        }
        return $this->mMktLevelerDetailAdj;
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
        $this->mSegAdjDelta = $this->getSegAdj() - $subj->getSegAdj();
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
        global $PROPID, $GEOID, $SITUS, $NEIGHB, $OWNER, $NEIGHBMIA, $MARKETVALUE, $LIVINGAREA, $SALEDATE, $SALEPRICE, $SALESOURCE, $SALETYPE,
               $IMPROVEMENTCNT, $HIGHVALIMPMARCN, $COMPLETE, $LANDVALUEADJ, $CLASSADJ, $ACTUALYEARBUILT,
               $GOODADJ, $LASIZEADJ, $MKTLEVELERDETAILADJ, $SEGMENTSADJ;

        //echo "setting field " .$fieldConst." to ".$value."<br>";
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
                $this->mLandValAdj = $value;
                break;
            case($CLASSADJ["NAME"]):
                $this->classAdj = $value[0].$value[1];
                break;
            case($ACTUALYEARBUILT["NAME"]):
                $this->mYearBuilt = $value;
                break;
            case($GOODADJ["NAME"]):
                $this->mGoodAdj = $value;
                break;
            case($LASIZEADJ["NAME"]):
                $this->setLASizeAdj($value);
                break;
            case($MKTLEVELERDETAILADJ["NAME"]):
                $this->mMktLevelerDetailAdj = $value;
                break;
            case($SEGMENTSADJ["NAME"]):
                $this->mSegAdj = $value;
            default:
                error_log("ERROR: UNKNOWN field on set: " . $fieldConst);
        }

    }

    /**
     * @param string $field
     * @return string|void
     */
    function getFieldByName($field)
    {
        global $PROPID, $GEOID, $SITUS, $NEIGHB, $OWNER, $NEIGHBMIA, $MARKETVALUE, $MARKETPRICESQFT,
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
            case($MARKETVALUE["NAME"]):
                return number_format($this->mMarketVal);
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
                return number_format($this->mLandValAdjDelta);
            case($CLASSADJ["NAME"]):
                return $this->getClassAdj();
            case($classadjdelta):
                return number_format($this->classAdjDelta);
            case($ACTUALYEARBUILT["NAME"]):
                return $this->mYearBuilt;
            case($GOODADJ["NAME"]):
                if ($this->mSubj) {
                    $year = date("Y");
                    if ($this->mYearBuilt <= ($year - 25)) {
                        if ($this->mGoodAdj >= 75) {
                            return $this->mGoodAdj . "_";
                        }
                    }
                }
                return $this->mGoodAdj;
            case($goodadjdelta):
                return number_format($this->mGoodAdjDelta);
            case($LASIZEADJ["NAME"]):
                return $this->getLASizeAdj();
            case($lasizeadjdelta):
                return number_format($this->mLASizeAdjDelta);
            case($HIGHVALIMPMASQFTDIFF["NAME"]):
                return $this->mHVImpSqftDiff;
            case($MKTLEVELERDETAILADJ["NAME"]):
                return number_format($this->mMktLevelerDetailAdj);
            case($mktlevelerdetailadjdelta):
                return number_format($this->mMktLevelerDetailAdjDelta);
            case($SEGMENTSADJ["NAME"]):
                return $this->mSegAdj;
            case($SEGMENTSADJSIMPLE["NAME"]):
                return (string)$this->getSegAdj();
            case($segmentsadjMultiRow):
                return $this->getImpDets();
            case($NETADJ["NAME"]):
                return number_format($this->mNetAdj);
            case($INDICATEDVAL["NAME"]):
                return number_format($this->getIndicatedVal());
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
            return $this->mMarketVal;
        }
        $var = null;
        if ($equityComp) {
            $var1 = $this->mMarketVal;
        } else {
            $var1 = $this->mSalePrice;
        }
        $result = $var1 + $this->getNetAdj();
        return $result;
    }

    function setIndicatedVal($val){
        $this->mIndVal = $val;
    }

    function getIndicatedVal(){
        return $this->mIndVal;
    }

    /**
     * @return int|null
     */
    function getNetAdj()
    {
        if ($this->isSubj() == true) {
            //Don't adjust a subject
            return NULL;
        }

        $var1 = $this->getLandValueAdjDelta();
        $var2 = $this->getClassAdjDelta();
        //No longer in sheet as of 2015
        //$var3 = (int)$this->mLASizeAdjDelta;
        $var4 = $this->getGoodAdjDelta();
        //No longer in sheet as of 2015
        //$var5 = (int)$this->mMktLevelerDetailAdjDelta;
        $var6 = $this->getImpDetSegAdj();

        $this->mNetAdj = (int)($var1 + $var2 + $var4 + $var6);

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

        $option = 2016;
        if ($this->classAdjDelta != null)
            return;

        if ($option == 2016) {
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

    private function getUnitPrice()
    {
        if ($this->mUnitPrice != null)
            return $this->mUnitPrice;

        global $UNITPRICE;
        $value = 0.0;

        $query = "SELECT " . $UNITPRICE["FIELD"] . " FROM `" . $UNITPRICE["TABLE"] . "` WHERE `prop_id`=" . $this->propId . " AND `det_use_unit_price` LIKE 'T'";

        $result = doSqlQuery($query);
        if (!$result)
            return "No Hits returned";

        $num = mysqli_num_rows($result);

        if ($num == 0)
            return "No Value Found!";

        while ($row = mysqli_fetch_array($result)) {
            $value += $row[$UNITPRICE["FIELD"]];
        }

        $this->mUnitPrice = $value;
        return $value;
    }

    /**
     * @return int
     */
    function getGoodAdjDelta()
    {
        return $this->mGoodAdjDelta;
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
        return ($this->getIndicatedVal() / $this->getLivingArea());
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
} // end of class
