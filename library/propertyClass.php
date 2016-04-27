<?php
include_once "defines.php";
include_once "ImpHelper.php";

class propertyClass{

	private $mSubj;

    private $mPropID;
	public $mGeoID;
	public $mSitus;
	public $mNeighborhood;
	public $mOwner;
	public $mHoodMIA;
	public $mMarketVal;
    private $mLivingArea;
	public $mSaleDate;
	private $mSalePrice;
	public $mSaleSource;
	public $mSaleType;
	public $mImprovCount;
	private $mIndVal;
	/* @deprecated */
	public $mHighValImpMARCN;
	public $mPercentComp;
    /**
     * @var int
     */
	private $mLandValAdj;
	public $mLandValAdjDelta;
    private $mClassAdj;
    private $mClassAdjDelta;
	public $mYearBuilt;
	public $mGoodAdj;
	public $mGoodAdjDelta;
	public $mLASizeAdj;
	public $mLASizeAdjDelta;
	public $mHVImpSqftDiff;
	public $mHVIMARCNarea; // Area of the HV Imp RCN
	private $mHVIMARCNareaPerSqft;
	public $mMktLevelerDetailAdj;
	public $mMktLevelerDetailAdjDelta;
	public $mSegAdj;
	public $mSegAdjDelta;

	public $mNetAdj;

	public $mMeanVal;
	public $mMedianVal;

	public $mAgent;

	private $mUnitPrice;

	private $mPrimeImpId;

	//Added in 2016 to list Improvemnts
	/**
	 * @var ImprovementDetailClass()
	 */
	private $mImpDets;

	// The constructor, duh!
	function __construct($propid=NULL){
		$this->mPropID = $propid;
		$this->mHoodMIA = NULL;
		$this->mImpDets = Array();
	}

    /**
     * @return bool
     */
    public function isSubj()
    {
        return $this->mSubj;
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
		return $this->mPropID;
	}

	/**
	 * @param null $mPropID
	 */
	public function setPropID($PropID)
	{
		$this->mPropID = $PropID;
	}

    /**
     * @return int
     */
    public function getLivingArea()
    {
        return $this->mLivingArea;
    }

    /**
     * @param int $mLivingArea
     */
    public function setLivingArea($mLivingArea)
    {
        $this->mLivingArea = $mLivingArea;
    }

    /**
     * @return int
     */
    public function getSalePrice()
    {
        return $this->mSalePrice;
    }

    /**
     * @param int $mSalePrice
     */
    public function setSalePrice($mSalePrice)
    {
        $this->mSalePrice = $mSalePrice;
    }

    /**
     * @return int
     */
    function getLandValAdj(){

        if($this->mLandValAdj != null){
            return $this->mLandValAdj;
        }

        $query = "SELECT land_hstd_val + land_non_hstd_val as result
                  FROM PROP WHERE prop_id = ". $this->mPropID;;

        $result = doSqlQuery($query);
        $num=mysqli_num_rows($result);

        if(!$result)
            return "No Value Found!";

        $row = mysqli_fetch_array($result);
        $this->mLandValAdj = $row['result'];
        return $this->mLandValAdj;
    }

	/**
	 * @param int $mLandValAdj
	 */
	public function setLandValAdj($LandValAdj)
	{
		$this->mLandValAdj = (int) $LandValAdj;
	}

    /**
     * @return int
     */
    function getLandValueAdjDelta(){
        return $this->mLandValAdjDelta;
    }

    /**
     * @param propertyClass $subj
     */
    function setLandValAdjDelta($subj){
        $this->mLandValAdjDelta = $subj->getLandValAdj() - $this->getLandValAdj();
    }

    /**
     * @return mixed
     */
    public function getMarketVal()
    {
        return $this->mMarketVal;
    }

    /**
     * @param mixed $mMarketVal
     */
    public function setMarketVal($mMarketVal)
    {
        $this->mMarketVal = $mMarketVal;
    }


	/**
	 * @return array
	 */
	public function getImpDets()
	{
		return $this->mImpDets;
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
	
	

	/**
	 * @return ImprovementDetailClass
	 */
	public function getMostValueImpUnit(){
		foreach ($this->getImpDets() as $impDet){
			/* @var $impDet ImprovementDetailClass */
			if($impDet->isDetUseUnitPrice()){
				return $impDet;
			}
		}
		error_log("Unable to find most valuable improvement for ".$this->mPropID);
		return null;
	}

	function __toString(){
		return json_encode($this);
	}

	private function getUnitPrice()
	{
		if($this->mUnitPrice != null)
			return $this->mUnitPrice;

		global $UNITPRICE;
		$value = 0.0;

		$query = "SELECT ".$UNITPRICE["FIELD"]." FROM `".$UNITPRICE["TABLE"]."` WHERE `prop_id`=".$this->mPropID." AND `det_use_unit_price` LIKE 'T'";

		$result=doSqlQuery($query);
		if(!$result)
			return "No Hits returned";

		$num=mysqli_num_rows($result);

		if($num==0)
			return "No Value Found!";

		while($row = mysqli_fetch_array($result))
		{
			$value += $row[$UNITPRICE["FIELD"]];
		}

		$this->mUnitPrice = $value;
		return $value;
	}

	function getNMIA()
	{
		global $NEIGHBMIA,$NEIGHB,$PROPID;
		$hoodval = $this->mNeighborhood;
		$prop_id = $this->mPropID;

		if($this->mHoodMIA != NULL)
			return $this->mHoodMIA;

		$query="SELECT * FROM ". $NEIGHBMIA["TABLE"] . " WHERE prop_id=$prop_id";
		//echo "getNMIA::query = " . $query . "<br>";
		$result = doSqlQuery($query);
		$num=mysqli_num_rows($result);

		if($num==0)
			return "No Value Found!";

		$row = mysqli_fetch_array($result);
		$adjarray = explode(";",$row[$NEIGHBMIA["FIELD"]],100);
		if(count($adjarray) ==0)
			return "No Value Found!";

		foreach($adjarray as $entry)
		{
			$entryarray = explode(":",$entry,2);
			if(count($entryarray) != 0)
			{
				if(strcmp(trim($hoodval),trim($entryarray[0]))== 0){
					$this->mHoodMIA = $entryarray[1];
					return $this->mHoodMIA;
				}
			}
		}

		return "No Value Found!";
	}

	// Pass the the SQL field to sum over for HVMARCN data
	function HighValFieldLookup($field,$data)
	{
		global $HIGHVALIMPMARCN,$allowablema;
		$mafield = "Imprv_det_type_cd";
		//sometimes we appear to look this up before the improvements...not sure why that's so, but this hack saves the day
		if($data === null){
			$this->getImpCount();
			$data = $this->mPrimeImpId;
		}
		//$query = "SELECT * FROM ".$HIGHVALIMPMARCN["TABLE"]." WHERE prop_id='$propid'";
		$subquery = "";
		$i=0;
		while($i < count($allowablema))
		{
			$subquery .= "imprv_det_type_cd='$allowablema[$i]'";
			if (++$i < count($allowablema))
				$subquery .= " OR ";
		}
		$query = "SELECT $field FROM IMP_DET, SPECIAL_IMP
		WHERE IMP_DET.prop_id='$this->mPropID'
		AND ( " . $subquery . ")
		AND imprv_det_id = det_id
		AND IMP_DET.prop_id = SPECIAL_IMP.prop_id";

		$query .= " AND IMP_DET.imprv_id = '$data'";
		//echo "$query";
		$result=doSqlQuery($query);
		if(!$result){
			return "No Value Found!";
		}
		$value=0;
		while($row = mysqli_fetch_array($result))
		{
			$value += $row[$field];
		}
		return $value;
	}

	function getClassAdj(){
		global $CLASSADJ,$HIGHVALIMPMARCN,$PROPID;
		$impids = array();
		$propid = $this->mPropID;

		//Changed in 2015 because found 'T' to be singular on property in SPECIAL_IMP
		/*$query = "SELECT det_class_code,det_subclass FROM IMP_DET, SPECIAL_IMP
			WHERE IMP_DET.prop_id='$propid'
			AND imprv_det_type_cd = '1ST' AND imprv_det_id = det_id AND IMP_DET.prop_id=SPECIAL_IMP.prop_id
			AND SPECIAL_IMP.det_use_unit_price LIKE 'T'";*/

		$query = "SELECT det_class_code,det_subclass FROM SPECIAL_IMP WHERE prop_id='$propid' AND det_use_unit_price LIKE 'T'";

		$result=doSqlQuery($query);

		if(!$result)
			return "No Value Found!";

		$resultarray = mysqli_fetch_array($result);

		return $resultarray;
	}


	/**
	 * @param propertyClass $subjdetailadj
	 */
	function setClassAdjDelta($subjdetailadj){
		global $debugquery;

		$option = 2016;
		if($this->mClassAdjDelta != null)
			return;

		if($option == 2016){
			// Added for 2016
			// According to TCAD : Comp Mkt Value * ( Subj Most Valuable Improvement unit - Comp Most Valuable Improvement unit)
			$subjMostValuableImpUnit = $subjdetailadj->getMostValueImpUnit()->getDetUnitprice();
			$compMostValuebleImpUnit = $this->getMostValueImpUnit()->getDetUnitprice();
			$result = $this->mLivingArea * ($subjMostValuableImpUnit - $compMostValuebleImpUnit);
			$this->mClassAdjDelta = round($result);
		}else if($option == 2015){
			//This was the 09 Formula Variables that we returned to in '14
			$var1 = number_format($subjdetailadj->getHVImpMARCNPerSQFT(),2);
			$var2 = number_format($this->getHVImpMARCNPerSQFT(),2);
			if($var2 == 0){
				//happens when property is missing improvements
				$this->mClassAdjDelta = 0;
				return;
			}
			$var3 = $var1/$var2;
			$var4 = $var3 - 1;
			$result = $var4 * $this->mHighValImpMARCN;
			if($debugquery) error_log("setClassAdjDelta[".$this->mPropID."]: ((".$var1."/".$var2.") - 1) *".$this->mHighValImpMARCN." = ".$result);
			$this->mClassAdjDelta = $result;
		}
		else
		{
			//2010~ version of class adj
			$var1 = $subjdetailadj->getUnitPrice();
			$var2 = $this->getUnitPrice();
			$var3 = $var1/$var2;
			$var4 = $var3 - 1;
			$result = $var4 * $var2 * $this->mLivingArea;
			$this->mClassAdjDelta = $result;
		}
	}

	/**
	 * @return int
	 */
	function getClassAdjDelta(){
		return $this->mClassAdjDelta;
	}

	function getGoodAdj(){
		global $PROPID,$GOODADJ,$allowablema,$mafield;
		$goodfield = $GOODADJ["FIELD"];
		$goodtable = $GOODADJ["TABLE"];
		$propid = $this->mPropID;
		$imprvidtable = "IMP_DET";

		$subquery = "";
		$i=0;
		while($i < count($allowablema))
		{
			$subquery .= "$mafield = '$allowablema[$i]'";
			if (++$i < count($allowablema))
				$subquery .= " OR ";
		}
		$query = "SELECT $goodfield FROM $goodtable,$imprvidtable
			WHERE $imprvidtable.prop_id='$propid'
			AND $goodtable.prop_id='$propid'
			AND $imprvidtable.Imprv_det_id = $goodtable.det_id
			AND ( " . $subquery . ")";

		//echo "$query";
		$result=doSqlQuery($query);

		if(!$result)
			return "No Value Found!";

		$row = mysqli_fetch_array($result);
		return $row[$goodfield];
	}

    /**
     * @return int
     */
	function getGoodAdjDelta(){
		return $this->mGoodAdjDelta;
	}

    /**
     * @param propertyClass $subjdetailadj
     */
	function setGoodAdjDelta($subj){
		global $isEquityComp;

        $option = 2016;

		if($isEquityComp)
			$var1 = $this->getMarketVal();
		else
			$var1 = $this->getSalePrice();

        if($option == 2016){
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

	function getSecondaryImp(){
		// Will return the sum of non-primeid values of improv_val in Special_IMP
		if($this->getImpCount() == 1)
		{
			return 0;
		}

		$value = 0;


		return $value;
	}

	function getImpDetCount(){
		return count($this->mImpDets);
	}

	function getLASizeAdj(){
		global $allowablema,$debugquery;

		if ($this->mLASizeAdj != null)
			return $this->mLASizeAdj;

		$propid = $this->mPropID;
		$subquery = "";

		$i=0;
		while($i < count($allowablema))
		{
			$subquery .= "imprv_det_type_cd='$allowablema[$i]'";
			if (++$i < count($allowablema))
				$subquery .= " OR ";
		}

		$query = "SELECT det_area FROM IMP_DET, SPECIAL_IMP
				WHERE IMP_DET.prop_id='$propid'
				AND ( " . $subquery . ")
				AND imprv_det_id = det_id
				AND IMP_DET.prop_id = SPECIAL_IMP.prop_id
				AND IMP_DET.imprv_id = '$this->mPrimeImpId'";

		if($debugquery) error_log("getLASizeAdj[".$this->mPropID."]: query=".$query);
		$result=doSqlQuery($query);
		$num=mysqli_num_rows($result);

		if(!$result)
			return "No Value Found!";

		$value=0;

		while($row = mysqli_fetch_array($result))
		{
			$value += $row["det_area"];
		}

		return $value;
	}

	function setLASizeAdjDelta($subjdetailadj){
		global $debugquery;

		if($this->mSubj == true)
			return NULL;
		if($this->mLASizeAdjDelta != null)
			return;
		$var1 = $subjdetailadj->mLASizeAdj;
		if($this->mLASizeAdj === null)
			$this->mLASizeAdj = $this->getLASizeAdj();
		$var2 = $this->mLASizeAdj;
		$var3 = number_format($subjdetailadj->getHVImpMARCNPerSQFT(),2);

		$this->mLASizeAdjDelta = ($var1-$var2)*$var3;
		if($debugquery) error_log("getLASizeAdjDelta: (".$var1."-".$var2.")*".$var3." = ".$this->mLASizeAdjDelta);
		//Now that we have a LASizeAdjDelta we can also compute the HVIMASQFTDIFF
		$this->setHVImpSqftDiff($subjdetailadj);
	}

	function setHVImpSqftDiff($subjdetailadj){
		$var1 = $subjdetailadj->mLASizeAdj;
		$var2 = $this->mLASizeAdj;

		$this->mHVImpSqftDiff = ($var1-$var2);
	}

	function getHVImpSqftDiff($subjdetailadj){
		return $this->mHVImpSqftDiff;
	}

	function getMktLevelerDetailAdj()
	{
		if($this->mMktLevelerDetailAdj === null)
		{
			global $MKTLEVELERDETAILADJ,$TABLE_IMP_DET,$TABLE_SPEC_IMP,$allowablema,$mafield,$debugquery;
			$propid = $this->mPropID;
			$subquery = "";
			$target = "det_calc_val";
			$target2 = "det_val";

			$i=0;
			while($i < count($allowablema))
			{
				$subquery .= "imprv_det_type_cd != '$allowablema[$i]'";
				if (++$i < count($allowablema))
					$subquery .= " AND ";
			}

			$query = "SELECT SPECIAL_IMP.$target,SPECIAL_IMP.$target2 FROM $TABLE_SPEC_IMP,$TABLE_IMP_DET
			WHERE $TABLE_IMP_DET.prop_id = '$propid' AND $TABLE_SPEC_IMP.prop_id = '$propid'
			AND $TABLE_IMP_DET.Imprv_det_id = $TABLE_SPEC_IMP.det_id
			AND ( " . $subquery . ")";

			if($this->getImpCount() > 1){
				$query .=" AND IMP_DET.imprv_id = '$this->mPrimeImpId'";
			}
			if($debugquery) error_log("getMktLevelerDetailAdj[".$this->mPropID."]: query=".$query);
			$result=doSqlQuery($query);

			if(!$result){
				return "No Value Found!";
			}
			$num=mysqli_num_rows($result);

			if($num == 0 ){
				return "No Value Found!";
			}
			$value=0;

			while($row = mysqli_fetch_array($result))
			{
				if($row[$target] == 0)
					$value += $row[$target2];
				else
					$value += $row[$target];
			}
			$this->mMktLevelerDetailAdj = $value;
		}
		return $this->mMktLevelerDetailAdj;
	}
	function setMktLevelerDetailAdjDelta($subjdetailadj){
		$this->mMktLevelerDetailAdjDelta = (int)$subjdetailadj->mMktLevelerDetailAdj - (int)$this->getMktLevelerDetailAdj();
	}

	function getSalePerSQFT(){
		if($this->mLivingArea == 0){
			return null;
		}
		return $this->mSalePrice/$this->mLivingArea;
	}

	function getMrktSqft(){
		if($this->mLivingArea == 0){
			return null;
		}
		return $this->mMarketVal/$this->mLivingArea;
	}

	function getHVImpMARCN()

	{
		if($this->mHighValImpMARCN === null)
		{
			$this->mHighValImpMARCN = $this->HighValFieldLookup('det_calc_val',$this->mPrimeImpId);
		}
		return $this->mHighValImpMARCN;
	}

	function getHVImpMARCNPerSQFT(){
		if($this->mLivingArea == 0){
			return null;
		}
		if($this->mHVIMARCNareaPerSqft === null){
			if($this->mImprovCount == 1){
				$this->mHVIMARCNareaPerSqft = $this->getHVImpMARCN()/$this->mLivingArea;
			}
			else{
				if($this->mHVIMARCNarea === null){
					$this->mHVIMARCNarea = $this->HighValFieldLookup('imprv_det_area',$this->mPrimeImpId);
					if($this->mHVIMARCNarea === null){
						error_log("ERROR> Unable to find improvements for ".$this->mPropID);
						return null;
					}
				}
				$this->mHVIMARCNareaPerSqft = $this->getHVImpMARCN()/$this->mHVIMARCNarea;
			}
		}
		return $this->mHVIMARCNareaPerSqft;
	}

	function getYearBuilt(){
		global $TABLE_IMP_DET;

		if($this->mYearBuilt != null)
			return $this->mYearBuilt;

		$query="SELECT yr_built FROM " . $TABLE_IMP_DET . " WHERE prop_id='$this->mPropID' AND imprv_id='$this->mPrimeImpId';";
		$result=doSqlQuery($query);

		if(!$result)
			return "No Value Found!";

		$num=mysqli_num_rows($result);
		$row = mysqli_fetch_array($result);
		$this->mYearBuilt = $row['yr_built'];
		return $this->mYearBuilt;
	}

	function lookupHVImpMARCN($data)
	{
		global $HIGHVALIMPMARCN,$allowablema;

		$mafield = "Imprv_det_type_cd";

		//sometimes we appear to look this up before the improvements...not sure why that's so, but this hack saves the day
		if($data === null){
			$this->getImpCount();
			$data = $this->mPrimeImpId;
		}
		//$query = "SELECT * FROM ".$HIGHVALIMPMARCN["TABLE"]." WHERE prop_id='$propid'";

		$subquery = "";
		$i=0;

		while($i < count($allowablema))
		{
			$subquery .= "imprv_det_type_cd='$allowablema[$i]'";
			if (++$i < count($allowablema))
				$subquery .= " OR ";
		}

		$query = "SELECT det_calc_val FROM IMP_DET, SPECIAL_IMP
		WHERE IMP_DET.prop_id='$this->mPropID'
		AND ( " . $subquery . ")
		AND imprv_det_id = det_id
		AND IMP_DET.prop_id = SPECIAL_IMP.prop_id";

		$query .= " AND IMP_DET.imprv_id = '$data'";
		//echo "$query";

		$result=doSqlQuery($query);

		if(!$result)
			return "No Value Found!";

		$value=0;
		while($row = mysqli_fetch_array($result))
		{
			$value += $row['det_calc_val'];
		}
		return $value;
	}

	function getImpCount(){

		if($this->mImprovCount != null)
			return $this->mImprovCount;

		$this->mImprovCount = count(ImpHelper::getUniqueImpIds($this->getImpDets()));
		$this->mPrimeImpId = ImpHelper::getPrimaryImpId($this->getImpDets());

		return $this->mImprovCount;
	}

	/**
	 * @param mixed $mSegAdj
	 */
	public function setSegAdj($SegAdj)
	{
		$this->mSegAdj = $SegAdj;
	}
	
	function getSegAdj(){
		global $TABLE_SPEC_IMP;

		if ($this->getImpCount() == 0){
			return 0;
		}

		if ($this->mSegAdj != null) {
			return $this->mSegAdj;
		}

		$query="SELECT DISTINCT imprv_id,imprv_val FROM " . $TABLE_SPEC_IMP ." as specimp WHERE prop_id='$this->mPropID';";

		$result=doSqlQuery($query);

		if(!$result)
			return "No Value Found!";

		$num=mysqli_num_rows($result);
		$value = 0;

		while($row = mysqli_fetch_array($result))
		{
			if($this->mPrimeImpId != $row['imprv_id'])
			{
				$value = $row['imprv_val'];
			}
		}

		$this->mSegAdj = $value;
		return $value;
	}

	/**
     * Gets the sum of deltas on the primary and secondary improvments
	 * @return int 
	 */
	public function getImpDetSegAdj(){
		$sum = ImpHelper::calcDeltaSum($this->getImpDets());
        $sum += $this->getSegAdjDelta();
        return $sum;
	}

	function setSegAdjDelta($subj){
		$this->mSegAdjDelta = $subj->getSegAdj() - $this->mSegAdj;
	}

	/**
	 * @return mixed
	 */
	function getSegAdjDelta(){
		return $this->mSegAdjDelta;
	}

	function getIndicatedVal(){
		if ($this->mIndVal === NULL){
			global $isEquityComp;
			if($this->mSubj == true){
				return $this->mMarketVal;
			}
			if($isEquityComp)
				$var1 = $this->mMarketVal;
			else
				$var1 = $this->mSalePrice;
			$this->mIndVal =  $var1 + $this->getNetAdj();
		}
		return $this->mIndVal;
	}

	function getIndicatedValSqft(){
		if($this->isSubj() == true)
			return NULL;
		if($this->getLivingArea() == 0)
			return NULL;
		return ($this->getIndicatedVal() / $this->getLivingArea());
	}

    /**
     * @return int|null
     */
	function getNetAdj()
	{
		if($this->isSubj() == true) {
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

		$this->mNetAdj = (int)($var1+ $var2 + $var4 + $var6);

		return $this->mNetAdj;
	}

	function setMeanVal($numComps){
		if($this->mSubj == false)
			return NULL;
		$result = 0;
		for($j=1; $j <= $numComps; $j++)
		{
			$data = $_SESSION["comp".$j];
			$result = $result + $data->getIndicatedVal();
		}
		$this->mMeanVal = (int)($result / $numComps);
	}

	function getMeanValSqft(){
		if($this->mSubj == false)
			return null;
		return $this->mMeanVal / $this->mLivingArea;
	}

	function setMedianVal($numComps){
		if($this->mSubj == false)
			return null;
		$median = -1;
		$comparray = array();
		for($i=1;$i <= $numComps; $i++)
		{
			$data = $_SESSION["comp".$i];
			$comparray[$i-1] = $data->getIndicatedVal();
		}
		sort($comparray);
		//var_dump($comparray);
		if ($numComps % 2) {
			$median = $comparray[floor($numComps/2)];
		} else {
			$median = ($comparray[$numComps/2] + $comparray[($numComps/2) - 1]) / 2;
		}
		$this->mMedianVal = $median;
	}

	function getMedianValSqft(){
		if($this->mSubj == false)
			return null;

		return $this->mMedianVal / $this->mLivingArea;
	}

	function getAgent(){
		return $this->mAgent;
	}

	/**
	 * @deprecated
	 * @param $fieldConst
	 * @param $value
	 */
	function setField($fieldConst,$value){
		global $PROPID,$GEOID,$SITUS,$NEIGHB,$OWNER,$NEIGHBMIA,$MARKETVALUE,$LIVINGAREA,$SALEDATE,$SALEPRICE,$SALESOURCE,
			   $IMPROVEMENTCNT,$HIGHVALIMPMARCN,$COMPLETE,$LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,
			   $GOODADJ,$LASIZEADJ,$MKTLEVELERDETAILADJ,$SEGMENTSADJ;

		//echo "setting field " .$fieldConst." to ".$value."<br>";
		if($fieldConst === NULL)
			return;

		if(is_string($value)){
			$value = trim($value);
		}
		switch($fieldConst)
		{
			case($PROPID[0]):
				$this->mPropID = $value;
				break;
			case($GEOID[0]):
				$this->mGeoID = $value;
				break;
			case($SITUS[0]):
				$this->mSitus = $value;
				break;
			case($NEIGHB[0]):
				$this->mNeighborhood = $value;
				break;
			case($NEIGHBMIA[0]):
				$this->mHoodMIA = $value;
				break;
			case($OWNER[0]):
				$this->mOwner = $value;
				break;
			case($MARKETVALUE[0]):
				$this->mMarketVal = $value;
				break;
			case($LIVINGAREA[0]):
				$this->mLivingArea = $value;
				break;
			case($SALEDATE[0]):
				$this->mSaleDate = $value;
				break;
			case($SALEPRICE[0]):
				$this->mSalePrice = $value;
				break;
			case($SALESOURCE[0]):
				$this->mSaleSource = $value;
				break;
			case($IMPROVEMENTCNT[0]):
				$this->mImprovCount = $value;
				break;
			case($HIGHVALIMPMARCN[0]):
				$this->mHighValImpMARCN = $value;
				break;
			case($COMPLETE[0]):
				$this->mPercentComp = $value;
				break;
			case($LANDVALUEADJ[0]):
				$this->mLandValAdj = $value;
				break;
			case($CLASSADJ[0]):
				$this->mClassAdj = $value[0].$value[1];
				break;
			case($ACTUALYEARBUILT[0]):
				$this->mYearBuilt = $value;
				break;
			case($GOODADJ[0]):
				$this->mGoodAdj = $value;
				break;
			case($LASIZEADJ[0]):
				$this->mLASizeAdj = $value;
				break;
			case($MKTLEVELERDETAILADJ[0]):
				$this->mMktLevelerDetailAdj = $value;
				break;
			case($SEGMENTSADJ[0]):
//				if($value != 0)
					$this->mSegAdj = $value;
//				else
//					$this->mSegAdj = 1;
//				break;
			default:
				//echo "ERROR: UNKNOWN field on set: " . $fieldConst.PHP_EOL;
		}

	}

	/**
	 * @param string $field
	 * @return string|void
	 */
	function getFieldByName($field){
		global $PROPID,$GEOID,$SITUS,$NEIGHB,$OWNER,$NEIGHBMIA,$MARKETVALUE,$MARKETPRICESQFT,
			   $LIVINGAREA,$SALEDATE,$SALEPRICE,$SALEPRICESQFT,
			   $IMPROVEMENTCNT,$HIGHVALIMPMARCN,$HIGHVALIMPMARCNSQFT,$COMPLETE,
			   $LANDVALUEADJ,$CLASSADJ,$ACTUALYEARBUILT,$GOODADJ,$LASIZEADJ,$HIGHVALIMPMASQFTDIFF,$MKTLEVELERDETAILADJ,$SEGMENTSADJ,
			   $NETADJ,$INDICATEDVAL,$INDICATEDVALSQFT,$MEANVAL,$MEANVALSQFT,$MEDIANVAL,$MEDIANVALSQFT;

		global $landvaladjdelta,$classadjdelta,$goodadjdelta,$lasizeadjdelta,$mktlevelerdetailadjdelta,
			   $segmentsadjdelta,$segmentsadjMultiRow;

		if($field === NULL)
			return;

		switch($field)
		{
			case($PROPID[0]):
				return $this->mPropID;
			case($GEOID[0]):
				return $this->mGeoID;
			case($SITUS[0]):
				return $this->mSitus;
			case($NEIGHB[0]):
				return $this->mNeighborhood;
			case($NEIGHBMIA[0]):
				return "0.00%";//$this->mHoodMIA;
			case($OWNER[0]):
				return $this->mOwner;
			case($MARKETVALUE[0]):
				return number_format($this->mMarketVal);
			case($MARKETPRICESQFT[0]):
				return number_format($this->getMrktSqft(),2);
			case($LIVINGAREA[0]):
				return $this->mLivingArea; //($this->mLivingArea);
			case($SALEDATE[0]):
				if(isNotMLS($this))
					return $this->mSaleDate;
				else
					return $this->mSaleDate."_";
			case($SALEPRICE[0]):
				if(isFlaggableSaleType($this))
					return $this->mSalePrice.".";
				else
					return $this->mSalePrice;
			case($SALEPRICESQFT[0]):
				return number_format($this->getSalePerSQFT());
			case($IMPROVEMENTCNT[0]):
				return $this->mImprovCount;
			case($HIGHVALIMPMARCN[0]):
				return number_format($this->mHighValImpMARCN);
			case($HIGHVALIMPMARCNSQFT[0]):
				return number_format($this->getHVImpMARCNPerSQFT(),2);
			case($COMPLETE[0]):
				return $this->mPercentComp;
			case($LANDVALUEADJ[0]):
				return $this->mLandValAdj;
			case($landvaladjdelta):
				return number_format($this->mLandValAdjDelta);
			case($CLASSADJ[0]):
				return $this->mClassAdj;
			case($classadjdelta):
				return number_format($this->mClassAdjDelta);
			case($ACTUALYEARBUILT[0]):
				return $this->mYearBuilt;
			case($GOODADJ[0]):
				if($this->mSubj){
					$year = date("Y");
					if($this->mYearBuilt <= ($year - 25 )){
						if($this->mGoodAdj >= 75){
							return $this->mGoodAdj . "_";
						}
					}
				}
				return $this->mGoodAdj;
			case($goodadjdelta):
				return number_format($this->mGoodAdjDelta);
			case($LASIZEADJ[0]):
				return $this->mLASizeAdj;
			case($lasizeadjdelta):
				return number_format($this->mLASizeAdjDelta);
			case($HIGHVALIMPMASQFTDIFF[0]):
				return $this->mHVImpSqftDiff;
			case($MKTLEVELERDETAILADJ[0]):
				return number_format($this->mMktLevelerDetailAdj);
			case($mktlevelerdetailadjdelta):
				return number_format($this->mMktLevelerDetailAdjDelta);
			case($SEGMENTSADJ[0]):
				return $this->mSegAdj;
			case($segmentsadjMultiRow):
				return $this->getImpDets();
			case($NETADJ[0]):
				return number_format($this->mNetAdj);
			case($INDICATEDVAL[0]):
				return number_format($this->getIndicatedVal());
			case($INDICATEDVALSQFT[0]):
				return number_format($this->getIndicatedValSqft(),2);
			case($MEANVAL[0]):
				return number_format($this->mMeanVal);
			case($MEANVALSQFT[0]):
				return number_format($this->getMeanValSqft(),2);
			case($MEDIANVAL[0]):
				return number_format($this->mMedianVal);
			case($MEDIANVALSQFT[0]):
				return number_format($this->getMedianValSqft(),2);
			case($segmentsadjdelta):
				return number_format($this->mSegAdjDelta);
			default:
				return "ERROR: UKNOWN field on get:".$field.PHP_EOL;
		}
	}

	function emitXML(){
		echo nl2br('<PROPID>'.$this->mPropID.'</PROPID>');
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
	 * @param mixed $mSaleType
	 */
	public function setMSaleType($mSaleType)
	{
		$this->mSaleType = $mSaleType;
	}

	/**
	 * @return mixed
	 */
	public function getMSaleType()
	{
		return $this->mSaleType;
	}

	public function toJson(){
		return json_encode($this,JSON_PRETTY_PRINT);
	}
} // end of class
