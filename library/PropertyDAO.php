<?php
include_once "defines.php";
include_once "ImprovementDetailClass.php";
include_once "propertyClass.php";

class PropertyDAO{
    /**
     * @var pdo
     */
    protected $pdo;

    /**
     * @var
     */
    protected $db;

    /**
     * PropertyDAO constructor.
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     * @param int $dbport
     */
    public function __construct($host, $username, $password, $database, $dbport=3306){
        // Create connection
        $pdo = new PDO("mysql:host=".$host.";dbname=".$database, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
    }


    /**
     * @return queryResult
     */
    protected function doSqlQuery($query){
        global $debugquery;

        if($debugquery) error_log("query:".$query);
        $result=$this->mysqli->query($query);
        $this->mysqli->close();
        if($debugquery){
            if (!$result){
                error_log("false query came back:".$result);
            } else {
                error_log("query came back:".var_dump($result));
            }
        }
        return $result;
    }

    /**
     * Retrieves the corresponding row for the specified property ID.
     * All non-delta fields should be populated at end of this function
     * @param $propId
     * @return propertyClass
     */
    public function getPropertyById($propId) {
        /* @var propertyClass $property */
        $property = $this->getCoreProp($propId);
        $property->setImpDets($this->getImpDet($propId));

        $property->setPropId($propId);
        $property->setImprovCount(count(ImpHelper::getUniqueImpIds($property->getImpDets())));
        $property->setPrimeImpId(ImpHelper::getPrimaryImpId($property->getImpDets()));
        $property->setSegAdj(ImpHelper::getSecondaryImprovementsValue($property->getImpDets()));
        $property->setMktLevelerDetailAdj(ImpHelper::getMktLevelerDetailAdj($property->getImpDets()));
        $property->setUnitPrice(ImpHelper::calculateUnitPrice($property->getImpDets()));
        $property->mPercentComp = '100';

        return $property;
    }


    /**
     * @param $propId
     * @return array
     */
    public function getImpDet($propId) {
        $stmt = $this->pdo->prepare("SELECT id.imprv_id, 
                          LTRIM(RTRIM(si.adjust_perc)) as adjPercRaw,
                          si.det_val as detVal,
                          LTRIM(RTRIM(id.imprv_det_type_cd)) as imprv_det_type_cd, 
                          LTRIM(RTRIM(id.Imprv_det_type_desc)) as imprv_det_type_desc, 
                          si.det_area, si.det_unitprice, si.det_use_unit_price,
                          LTRIM(RTRIM(id.imprv_det_id)) as imprv_det_id,
                          si.imprv_val as imprv_val,
                          si.det_calc_val as det_calc_val
                  FROM SPECIAL_IMP si 
                  LEFT JOIN IMP_DET id  
                  ON si.imprv_id = id.imprv_id AND si.det_id = id.imprv_det_id
                  WHERE si.prop_id=:propId;");
        $stmt->bindValue(":propId", $propId, PDO::PARAM_INT);
        $stmt->execute();

        $impArray = Array();
        while($impDet = $stmt->fetchObject("ImprovementDetailClass")){
            $impArray[] = $impDet;
        }
        return $impArray;
    }

    /**
     * @var int $propId
     * @return propertyClass
     */
    private function getCoreProp($propId){
        $prop = new propertyClass();
        $prop->setPropID($propId);

        $stmt = $this->pdo->prepare("SELECT 
                        p.geo_id as mGeoID,
                        LTRIM(RTRIM(p.situs_street_prefx)) as situs_pre, 
                        LTRIM(RTRIM(p.situs_num)) as situs_num,
                        LTRIM(RTRIM(p.situs_street)) as situs_st, 
                        LTRIM(RTRIM(p.situs_street_suffix)) as situs_suf,
                        LTRIM(RTRIM(p.situs_unit)) as situs_unit,
                        LTRIM(RTRIM(p.situs_zip)) as situs_zip,
                        LTRIM(RTRIM(p.hood_cd)) as mNeighborhood,
                        LTRIM(RTRIM(p.py_owner_name)) as mOwner,
                        p.market_value as mMarketVal,
                        sp.liv_area as mLivingArea,
                        si.det_class_code as classCode,
                        si.det_subclass as subClass,
                        si.det_base_deprec_perc as mGoodAdj,
                        sp.yr_built as yrBuilt,
                        sp.eff_yr_built as efYrBuilt,
                        LTRIM(RTRIM(si.det_cond_code)) as cond,
                        LTRIM(RTRIM(p.imprv_state_cd)) as stCode
                    FROM
                        PROP p
                    LEFT JOIN
                        SPECIAL_PROPDATA sp
                    ON 
                        p.prop_id = sp.prop_id
					LEFT JOIN
						SPECIAL_IMP si
					ON
						p.prop_id = si.prop_id AND si.det_use_unit_price = 'T'
					LEFT JOIN
					    IMP_DET i
					ON 
					   p.prop_id = i.prop_id AND si.det_id = i.imprv_det_id
                    WHERE
                    p.prop_id = ?");
        $stmt->bindValue(1, $propId, PDO::PARAM_INT);
        $stmt->execute();

        $stmt->bindColumn('mGeoID', $prop->mGeoID, PDO::PARAM_STR);
        $stmt->bindColumn('situs_pre', $prop->situs_prefix, PDO::PARAM_STR);
        $stmt->bindColumn('situs_num', $prop->situs_number, PDO::PARAM_INT);
        $stmt->bindColumn('situs_st', $prop->situs_street, PDO::PARAM_STR);
        $stmt->bindColumn('situs_suf', $prop->situs_suffix, PDO::PARAM_STR);
        $stmt->bindColumn('situs_unit', $prop->situs_unit, PDO::PARAM_STR);
        $stmt->bindColumn('situs_zip', $prop->situs_zip, PDO::PARAM_INT);
        $stmt->bindColumn('mNeighborhood', $prop->mNeighborhood, PDO::PARAM_STR);
        $stmt->bindColumn('mOwner', $prop->mOwner, PDO::PARAM_STR);
        $stmt->bindColumn('mMarketVal', $prop->mMarketVal, PDO::PARAM_INT);
        $stmt->bindColumn('mLivingArea', $livingarea, PDO::PARAM_INT);
       // $stmt->bindColumn('mLivingArea', $mLASizeAdj, PDO::PARAM_INT);
        $stmt->bindColumn('classCode', $classcode, PDO::PARAM_STR);
        $stmt->bindColumn('subClass', $subclass, PDO::PARAM_STR);
        $stmt->bindColumn('mGoodAdj', $prop->mGoodAdj, PDO::PARAM_STR);
        $stmt->bindColumn('yrBuilt', $prop->mYearBuilt, PDO::PARAM_INT);
        $stmt->bindColumn('efYrBuilt', $prop->effectiveYearBuilt, PDO::PARAM_INT);
        $stmt->bindColumn('cond', $cond, PDO::PARAM_STR);
        $stmt->bindColumn('stCode', $prop->stateCode, PDO::PARAM_STR);


        $result = $stmt->fetch(PDO::FETCH_BOUND);
        if($result === false){
            throw new Exception("Unable to find property with propId=".$propId);
        }
        $prop->setLivingArea($livingarea);
        $prop->setClassCode($classcode);
        $prop->setSubClass($subclass);
        $prop->setCondition($cond);
        //Added for 2017 seems to be same as area of main
        $prop->setLASizeAdj($livingarea);

        $stmt2 = $this->pdo->prepare("SELECT SUM(p.land_hstd_val + p.land_non_hstd_val) as totHstd
                                        FROM PROP p WHERE p.prop_id = ?");
        $stmt2->bindValue(1, $propId, PDO::PARAM_INT);
        $stmt2->execute();

        $stmt2->bindColumn('totHstd', $landVal, PDO::PARAM_STR);
        $stmt2->fetch(PDO::FETCH_BOUND);

        $prop->setLandValAdj($landVal);
        return $prop;
    }

    /**
     * @param string $hood
     * @return propertyClass[]
     */
    public function getHoodProperties($hood,  queryContext $queryContext)
    {
        if($queryContext->isEquityComp){
            return $this->getHoodPropsEq($hood, $queryContext->multiHood);
        } else {
            return $this->getHoodPropsSales($hood, $queryContext);
        }
    }

    /**
     * @param string $hood
     * @param bool $multihood
     * @return propertyClass[]
     */
    private function getHoodPropsEq($hood, $multihood){
        if($multihood) {
            $hoodToUse = substr($hood, 0, -2);
            $hoodQuery =  " WHERE hood_cd LIKE '".$hoodToUse."%'";
        } else {
            $hoodToUse = $hood;
            $hoodQuery = " WHERE hood_cd = '".$hoodToUse."' ";
        }
        $query = "SELECT prop_id FROM PROP" . $hoodQuery;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $properties = array();
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $properties[] = $this->getPropertyById($row['prop_id']);
            }
        }

        return $properties;
     }

    protected function getHoodPropsSales($hood, $queryContext){
        $debug = false;

        if($queryContext->multiHood) {
            $hoodToUse = substr($hood, 0, -2);
            $hoodQuery =  " WHERE hood_cd LIKE '".$hoodToUse."%'";
        } else {
            $hoodToUse = $hood;
            $hoodQuery = " WHERE hood_cd = '".$hoodToUse."' ";
        }
        //Add on sale restrictions
        $year = date("Y");
        $years = " AND (s.sale_date LIKE '%".$year."%'";
        for($i=1; $i <= $queryContext->prevYear; $i++){
            $yearsBack = $year - $i ;
            $years = $years . " OR s.sale_date LIKE '%".$yearsBack."%' ";
        }
        $years = $years . ") ";
        $query="SELECT p.prop_id as prop_id, "
            . "s.sale_price as sale_price, "
            . "s.source as source, "
            . "s.sale_date as sale_date, "
            . "s.sale_type as sale_type "
            . "FROM PROP as p, SALES_MLS_MERGED as s"
            . $hoodQuery
            . $years
            . " AND s.sale_price>0 "
            . " AND p.prop_id = s.prop_id;";
        $stmt = $this->pdo->prepare($query);

        $properties = array();
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $currProp = $this->getPropertyById($row['prop_id']);
                $currProp->setSalePrice($row['sale_price']);
                $currProp->mSaleDate = $row['sale_date'];
                $currProp->setSaleSource($row['source']);
                if($row['sale_type'] === null && $currProp->getSaleSource() === 'MLS'){
                    $currProp->setSaleType('mls');
                } elseif ($row['sale_type']!= null ) {
                    $currProp->setSaleType($row['sale_type']);
                } else {
                    $currProp->setSaleType("don't ask");
                }
                $properties[] = $currProp;
            }
        }

        if($debug) var_dump($properties);
        return $properties;
    }
}