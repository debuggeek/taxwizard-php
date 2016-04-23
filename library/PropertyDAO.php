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
     * Retrieves the corresponding row for the specified user ID.
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
        
        $property->mPercentComp = '100';

        return $property;
    }


    /**
     * @param $propId
     * @return array
     */
    public function getImpDet($propId) {
        $query = "SELECT id.imprv_id, 
                          LTRIM(RTRIM(id.imprv_det_type_cd)) as imprv_det_type_cd, 
                          LTRIM(RTRIM(id.Imprv_det_type_desc)) as imprv_det_type_desc, 
                          si.det_area, si.det_unitprice, si.det_use_unit_price,
                          LTRIM(RTRIM(id.imprv_det_id)) as imprv_det_id,
                          si.imprv_val as imprv_val
                  FROM SPECIAL_IMP si 
                  LEFT JOIN IMP_DET id  
                  ON si.imprv_id = id.imprv_id AND si.det_id = id.imprv_det_id
                  WHERE si.prop_id=".$propId;
        $result = $this->pdo->query($query);
        $impArray = Array();
        while($impDet = $result->fetchObject("ImprovementDetailClass")){
            $impArray[] = $impDet;
        }
        return $impArray;
    }

    /**
     * @var int $propId
     * @return propertyClass
     */
    private function getCoreProp($propId){
        $query = "SELECT 
                        p.geo_id as mGeoID,
                        CONCAT_WS(' ', LTRIM(RTRIM(p.situs_street_prefx)), 
                                      LTRIM(RTRIM(p.situs_street)), 
                                      LTRIM(RTRIM(p.situs_street_suffix)),
                                      LTRIM(RTRIM(p.situs_unit)),
                                      LTRIM(RTRIM(p.situs_zip))) as mSitus,
                        LTRIM(RTRIM(p.hood_cd)) as mNeighborhood,
                        LTRIM(RTRIM(p.py_owner_name)) as mOwner,
                        p.market_value as mMarketVal,
                        sp.liv_area as mLivingArea,
                        SUM(p.land_hstd_val + p.land_non_hstd_val) as mLandValAdj,
                        CONCAT_WS('',si.det_class_code,si.det_subclass) as mClassAdj,
                        si.det_base_deprec_perc as mGoodAdj,
                        i.yr_built as mYearBuilt
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
                    p.prop_id = ".$propId;
        $result = $this->pdo->query($query);
//        $result->setFetchMode(PDO::FETCH_CLASS, 'propertyClass');
        return $result->fetchObject("propertyClass");
    }
}