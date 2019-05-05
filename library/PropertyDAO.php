<?php
include_once "defines.php";
include_once "ImprovementDetailClass.php";
include_once "propertyClass.php";

class PropertyDAO{
    /**
     * @var pdo
     */
    protected $pdo;

    /** @var
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
        try {
            $pdo = new PDO("mysql:host=" . $host . ";dbname=" . $database, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo = $pdo;
        } catch(PDOException $e) {
            error_log('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Retrieves the corresponding row for the specified property ID.
     * All non-delta fields should be populated at end of this function
     *
     * Will throw exception if property cannot be fully hydrated and valid
     * @param $propId
     * @return propertyClass
     * @throws Exception
     */
    public function getPropertyById($propId) {
        /* @var propertyClass $property */
        $property = $this->getCoreProp($propId);
        $property->setImpDets($this->getImpDet($propId));
        if(sizeof($property->getImpDets()) == 0){
            throw new Exception("Unable to find any improvements for ".$propId);
        }

        $property->setPropId($propId);
        $property->setImprovCount(count(ImpHelper::getUniqueImpIds($property->getImpDets())));
        $property->setPrimeImpId(ImpHelper::getPrimaryImpId($property->getImpDets()));
        if($property->getImprovCount() > 1) {
            $property->setSegAdj(ImpHelper::getSecondaryImprovementsValue($property->getImpDets()));
        } else {
            $property->setSegAdj(0);
        }
        //Following all based on primary improvement
        $primeImp = ImpHelper::getPrimaryImprovementRepresentative($property->getImpDets());
        $property->setSubClass($primeImp->getSubClass());
        $property->setCondition($primeImp->getCondCode());
        $property->setGoodAdj($primeImp->getGoodPerc());
        $property->setClassCode($primeImp->getClassCode());
        $property->setMktLevelerDetailAdj(ImpHelper::getMktLevelerDetailAdj($property->getImpDets()));
        $property->setUnitPrice(ImpHelper::calculateUnitPrice($property->getImpDets()));
        //This should be based on the primary improvement (highest value)
        $property->setLASizeAdj(ImpHelper::calcLASizeAdj($property->getImpDets()));
        $property->mPercentComp = '100';

        return $property;
    }


    /**
     * @param $propId
     * @return array
     */
    public function getImpDet($propId) {
        try{
            $stmt = $this->pdo->prepare("SELECT id.imprv_id, 
                              id.prop_id as prop_id,
                              LTRIM(RTRIM(si.adjust_perc)) as adjPercRaw,
                              si.det_val as detVal,
                              LTRIM(RTRIM(id.imprv_det_type_cd)) as imprv_det_type_cd, 
                              LTRIM(RTRIM(id.Imprv_det_type_desc)) as imprv_det_type_desc, 
                              si.det_area, si.det_unitprice, si.det_use_unit_price,
                              LTRIM(RTRIM(id.imprv_det_id)) as imprv_det_id,
                              si.imprv_val as imprv_val,
                              si.det_calc_val as det_calc_val,
                              si.det_class_code as class_code,
                              si.det_subclass as subClass,
                              si.det_base_deprec_perc as base_deprec_perc,
                              si.det_phy_perc as phy_perc,
                              si.det_func_perc as func_perc,
                              si.det_eco_perc as eco_perc,
                              LTRIM(RTRIM(si.det_cond_code)) as cond_code
                      FROM SPECIAL_IMP si 
                      LEFT JOIN IMP_DET id  
                      ON si.imprv_id = id.imprv_id AND si.det_id = id.imprv_det_id
                      WHERE si.perc_complete = 100 AND si.prop_id=:propId;");
            $stmt->bindValue(":propId", $propId, PDO::PARAM_INT);
            $stmt->execute();

            $impArray = Array();
            while($impDet = $stmt->fetchObject("ImprovementDetailClass")){
                $impArray[] = $impDet;
            }
            return $impArray;
        } catch (Exception $e){
            error_log("Error in getImpDet : " . $e->getMessage());
        }
        return [];
    }

    /**
     * @var int $propId
     * @return propertyClass
     * @throws Exception
     */
    private function getCoreProp($propId){
        $prop = new propertyClass();
        $prop->setPropID($propId);

        $stmt = $this->pdo->prepare(/** @lang MySQL */
            "SELECT 
                        p.geo_id as mGeoID,
                        LTRIM(RTRIM(p.situs_street_prefx)) as situs_pre, 
                        LTRIM(RTRIM(p.situs_num)) as situs_num,
                        LTRIM(RTRIM(p.situs_street)) as situs_st, 
                        LTRIM(RTRIM(p.situs_street_suffix)) as situs_suf,
                        LTRIM(RTRIM(p.situs_unit)) as situs_unit,
                        LTRIM(RTRIM(p.situs_zip)) as situs_zip,
                        LTRIM(RTRIM(p.hood_cd)) as mNeighborhood,
                        LTRIM(RTRIM(p.abs_subdv_cd)) as mSubdivision,
                        LTRIM(RTRIM(p.py_owner_name)) as mOwner,
                        p.market_value as mMarketVal,
                        sp.liv_area as mLivingArea,
                        sp.yr_built as yrBuilt,
                        sp.eff_yr_built as efYrBuilt,
                        LTRIM(RTRIM(p.imprv_state_cd)) as stCode
                    FROM
                        PROP p
                    LEFT JOIN
                        SPECIAL_PROPDATA sp
                    ON 
                        p.prop_id = sp.prop_id
					LEFT JOIN
					    IMP_DET i
					ON 
					   p.prop_id = i.prop_id
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
        $stmt->bindColumn('mSubdivision', $prop->mSubdivision, PDO::PARAM_STR);
        $stmt->bindColumn('mOwner', $prop->mOwner, PDO::PARAM_STR);
        $stmt->bindColumn('mMarketVal', $prop->mMarketVal, PDO::PARAM_INT);
        $stmt->bindColumn('mLivingArea', $livingarea, PDO::PARAM_INT);
        $stmt->bindColumn('yrBuilt', $prop->mYearBuilt, PDO::PARAM_INT);
        $stmt->bindColumn('efYrBuilt', $prop->effectiveYearBuilt, PDO::PARAM_INT);
        $stmt->bindColumn('stCode', $prop->stateCode, PDO::PARAM_STR);


        $result = $stmt->fetch(PDO::FETCH_BOUND);
        if($result === false){
            throw new Exception("Unable to find property with propId=".$propId);
        }
        $prop->setLivingArea($livingarea);

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
     * @throws Exception
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
                $propId = $row['prop_id'];
                try {
                    $properties[] = $this->getPropertyById($propId);
                } catch (Exception $e) {
                    error_log("Skipping property ".$propId." due to: ".$e->getMessage());
                }
            }
        }

        return $properties;
     }

/**
 * @param $hood
 * @param queryContext $queryContext
 * @return array
 * @throws Exception
 */
    protected function getHoodPropsSales($hood, $queryContext){
        $debug = false;
        $mlsSaleCount = 0;
        $tcadSaleCount = 0;
        try {
            if ($queryContext->multiHood) {
                $hoodToUse = substr($hood, 0, -2);
                $hoodQuery = " WHERE hood_cd LIKE '" . $hoodToUse . "%'";
            } else {
                $hoodToUse = $hood;
                $hoodQuery = " WHERE hood_cd = '" . $hoodToUse . "' ";
            }
            //Add on sale restrictions
            $year = date("Y");
            $years = " AND (s.sale_date LIKE '%" . $year . "%'";
            for ($i = 1; $i <= $queryContext->prevYear; $i++) {
                $yearsBack = $year - $i;
                $years = $years . " OR s.sale_date LIKE '%" . $yearsBack . "%' ";
            }
            $years = $years . ") ";
            $query = "SELECT p.prop_id as prop_id, "
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
                    $currPropId = $row['prop_id'];
                    try {
                        if ($queryContext->traceComps) error_log("attempting to get " . $row['prop_id']);
                        $currProp = $this->getPropertyById($currPropId);
                        if ($row['sale_price'] == null) {
                            throw new Exception("No sales info found");
                        }
                        $currProp->setSalePrice($row['sale_price']);
                        $currProp->mSaleDate = $row['sale_date'];
                        $currProp->setSaleSource($row['source']);
                        if ($row['sale_type'] === null && $currProp->getSaleSource() === 'MLS') {
                            $currProp->setSaleType('mls');
                            $mlsSaleCount++;
                        } elseif ($row['sale_type'] != null) {
                            $currProp->setSaleType($row['sale_type']);
                            $tcadSaleCount++;
                        } else {
                            $currProp->setSaleType("don't ask");
                        }
                        $properties[] = $currProp;
                    } catch (Exception $e){
                        $error = sprintf("Skipping propId:%s due to error:%s", $currPropId, $e->getMessage());
                        error_log("ERROR\tgetHoodPropsSales>>".$error);
                        $queryContext->responseCtx->errors[] = $error;
                    }
                }
            }

            if ($debug) var_dump($properties);
            $queryContext->responseCtx->unfilteredMLSSalesCount = $mlsSaleCount;
            $queryContext->responseCtx->unfilteredTCADSalesCount = $tcadSaleCount;

            return $properties;
        } catch (PDOException $e){
            error_log("DB Error " . $e->getMessage());
        }
        return [];
    }
}