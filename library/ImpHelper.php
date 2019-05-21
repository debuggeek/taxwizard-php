<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/2/16
 * Time: 6:34 PM
 */
include_once 'ImprovementDetailClass.php';

class ImpHelper
{
    const useAdjPerc = true;

    /**
     * Walks the primary improvements on subject and comps and finds deltas
     * If the comp doesn't have the primary improvement then one is added as a placeholder for the delta
     * @param ImprovementDetailClass[] $subjImps
     * @param ImprovementDetailClass[] $compImps
     * @return ImprovementDetailClass[]
     * @throws Exception
     */
    public static function compareImpDetails_AddDelta($subjImps, $compImps){

        //Walk the primary improvements and if the comp has it then calculate delta
        $compImpsUsed = array();
        foreach (self::getPrimaryImprovements($subjImps) as $subjImp){
            $found = false;
            /* @var $subjImp ImprovementDetailClass */
            foreach ($compImps as $compImp){
                /* @var $compImp ImprovementDetailClass */
                if($subjImp->getImprvDetTypeCd() == $compImp->getImprvDetTypeCd()){
                    if(!in_array($compImp->getImprvDetId(), $compImpsUsed)){
                        $found = true;
                        $compImp->setAdjustmentDelta(self::getDelta($subjImp, $compImp));
                        $compImpsUsed[] = $compImp->getImprvDetId();
                        break;
                    }
                }
            }
            if(!$found) {
                $newImp = new ImprovementDetailClass();
                $newImp->setImprvDetTypeCd($subjImp->getImprvDetTypeCd());
                $newImp->setImprvDetTypeDesc($subjImp->getImprvDetTypeDesc());
                $newImp->setDetArea("NONE");
                $newImp->setAdjustmentDelta(self::getDelta($subjImp, $newImp));
                //Since this is a primary improvment on the subject the delta is here
                $newImp->setImprvId(self::getPrimaryImpId($compImps));
                $compImps[] = $newImp;
            }
        }

        //Walk all compImps and any without a delta are unique to the comp
        foreach (self::getPrimaryImprovements($compImps) as $compImp) {
            /* @var $compImp ImprovementDetailClass */
            if($compImp->getAdjustmentDelta() === NULL){
                if(ImpHelper::isMainArea($subjImp->getImprvDetTypeCd())){
                    $result = $compImp->getDetArea() * $compImp->getDetUnitprice();
                    $compImp->setAdjustmentDelta(round($result));
                } else {
                    if(self::useAdjPerc){
                        $compValue = $compImp->getDetVal()  * $compImp->getAdjustedPerc();
                    } else {
                        $compValue = $compImp->getDetArea() * $compImp->getDetUnitprice();
                    }
                    //negative because we get to count against subject
                    $compImp->setAdjustmentDelta(round($compValue)  * -1);
                }
            }
        }

        return $compImps;
    }

    /**
     * @param ImprovementDetailClass() $propertyImps
     * @return int
     */
    public static function calcDeltaSum($propertyImps){
        $result = 0;
        foreach ($propertyImps as $impDet) {
            /* @var $impDet ImprovementDetailClass */
            $result += $impDet->getAdjustmentDelta();
        }
        return $result;
    }

    /**
     * Walks all improvements and returns array of all the improvement ids (duplicated)
     * one per improvement
     * @param $propertyImps
     * @return array
     */
    public static function getAllImpIds($propertyImps){
        $imps = array();
        foreach ($propertyImps as $improv) {
            /* @var $improv ImprovementDetailClass */
            $imps[] = $improv->getImprvId();
        }
        return $imps;
    }

    /**
     * Walks all improvements and returns array of all the improvement ids (distinct)
     * @param $propertyImps
     * @return array
     */
    public static function getUniqueImpIds($propertyImps){
        $impDetImprIdList = self::getAllImpIds($propertyImps);
        return array_unique($impDetImprIdList);
    }

    /**
     * Returns the improvement id used for the primary improvement
     * This is based on the improvement (in a multi-improvement property)
     * with the highest improvement value
     * @param array ImprovementDetailClass $propertyImps
     * @return int
     * @throws Exception
     */
    public static function getPrimaryImpId($propertyImps){
        $imprvIdValues = array();
        foreach ($propertyImps as $improv) {
            /* @var $improv ImprovementDetailClass */
            if($improv->isDetUseUnitPrice()){
                $imprvIdValues[$improv->getImprvId()] = $improv->getImprvVal();
            }
        }
        if(count($imprvIdValues) == 0){
            throw new Exception("No primary improvement found");
        }
        arsort($imprvIdValues);
        return key($imprvIdValues);
    }

    /**
     * Gets the list of primary improvements on a property
     * @param array ImprovementDetailClass $propertyImps
     * @return ImprovementDetailClass[]
     * @throws Exception
     */
    public static function getPrimaryImprovements($propertyImps){
        global $EXCLUDED_IMPROVEMENT_CODES;

        $primaryImpId = self::getPrimaryImpId($propertyImps);
        $primaryImprv = array();
        foreach ($propertyImps as $improv) {
            /* @var $improv ImprovementDetailClass */
            if($primaryImpId == $improv->getImprvId()){
                if(!in_array($improv->getImprvDetTypeCd(), $EXCLUDED_IMPROVEMENT_CODES)) {
                    $primaryImprv[] = $improv;
                }
            }
        }
        return $primaryImprv;
    }

    /**
     * Returns the representative improvement of the primary improvements
     * @param $propertyImps
     * @return ImprovementDetailClass
     * @throws Exception
     */
    public static function getPrimaryImprovementRepresentative($propertyImps){
        $primaryImprv = self::getPrimaryImprovements($propertyImps);
        foreach ($primaryImprv as $improv) {
            /* @var $improv ImprovementDetailClass */
            if($improv->isDetUseUnitPrice() == 'T'){
                return $improv;
            }
        }
        throw new Exception("Primary Improvement not found");
    }

    /**
     * Finds all the non-primary improvements
     * @param $propertyImps
     * @return array
     * @throws Exception
     */
    public static function getSecondaryImprovements($propertyImps){
        $primaryImpId = self::getPrimaryImpId($propertyImps);
        $secondaryImpr = array();
        foreach ($propertyImps as $improv) {
            /* @var $improv ImprovementDetailClass */
            if($primaryImpId != $improv->getImprvId()){
                $secondaryImpr[] = $improv;
            }
        }
        return $secondaryImpr;    
    }

    /**
     * @param $propertyImps
     * @return int
     * @throws Exception
     */
    public static function getSecondaryImprovementsValue($propertyImps){
        $secImps = self::getSecondaryImprovements($propertyImps);
        $secTotal = 0;
        foreach($secImps as $secImp){
            /* @var ImprovementDetailClass $secImp */
            if($secImp->isDetUseUnitPrice() == 'T'){
                $secTotal += $secImp->getImprvVal();
            }
        }
        if($secTotal > 0){
            return $secTotal;
        }
        // Making it here means that no improvement with True flag for use unit found
        foreach($secImps as $secImp) {
            //return first number we fine... I guess
            if(is_numeric($secImp->getImprvVal())){
                return $secImp->getImprvVal();
            }
        }
        throw new Exception("No value found for secondary improvement");
    }

    /**
     * Returns the MA area sum of the primary improvement
     * @param $propertyImps
     * @return int
     * @throws Exception
     */
    public static function calcLASizeAdj($propertyImps){
        $primeImps = self::getPrimaryImprovements($propertyImps);
        $improvementArea = 0;
        foreach($primeImps as $imp) {
            if (self::isMainArea($imp->getImprvDetTypeCd())) {
                $improvementArea += $imp->getDetArea();
            }
        }
        if($improvementArea == 0){
            throw new Exception("Found no primary improvement areas");
        }
        return $improvementArea;
    }

    /**
     * Finds the first improvement with a given code
     * @param $propertyImps
     * @param $impDetCode
     * @param null $exclusions[] list of detail ids to skip
     * @return ImprovementDetailClass|null
     */
    public static function getImprovObjByCode($propertyImps, $impDetCode, $exclusions = null){
        foreach ($propertyImps as $improv) {
            /* @var $improv ImprovementDetailClass */
            if ($impDetCode == $improv->getImprvDetTypeCd()) {
                if(in_array($improv->getImprvDetId(), $exclusions)){
                    //The exclusions list said not to return this detail id
                    continue;
                }
                return $improv;
            }
        }
        return null;
    }

    /**
     * @param ImprovementDetailClass $subjImp
     * @param ImprovementDetailClass $compImp
     * @return int
     * @throws Exception
     */
    private static function getDelta($subjImp, $compImp){
        if(ImpHelper::isMainArea($subjImp->getImprvDetTypeCd())){
            if($compImp->getDetArea() === NULL || $compImp->getDetArea() == "NONE"){
                //If the comp doesn't have the element then we use the subj unit price
                $result = $subjImp->getDetArea() * $subjImp->getDetUnitprice();
            } else {
                $areaDiff = $subjImp->getDetArea() - $compImp->getDetArea();
                $result = $areaDiff * $compImp->getDetUnitprice();
            }
            return round($result);
        } else {
            //The following was added based on email with TCAD and some of there secret magic they were doing
            if(self::useAdjPerc){
                $subjValue = $subjImp->getDetVal() * $subjImp->getAdjustedPerc();
                $compValue = $compImp->getDetVal()  * $compImp->getAdjustedPerc();
            } else {
                $subjValue = $subjImp->getDetArea() * $subjImp->getDetUnitprice();
                $compValue = $compImp->getDetArea() * $compImp->getDetUnitprice();
            }
            return round($subjValue - $compValue);
        }
    }

    /**
     * @param string $improvementCode
     * @return bool
     */
    private static function isMainArea($improvementCode){
        $allowableMainAreas= array("1/2","1ST","2ND","3RD","4TH","5TH","ADDL","ATRM","BELOW",
                                "CONC","DOWN","FBSMT","LOBBY","MEZZ","PBSMT","RSBLW","RSDN");
        return in_array($improvementCode, $allowableMainAreas);
    }

    /**
     * @param ImprovementDetailClass[] $getImpDets
     * @return int
     * @throws Exception
     */
    public static function getMktLevelerDetailAdj($getImpDets)
    {
        $primeImps = self::getPrimaryImprovements($getImpDets);
        $detValCalcSum = 0;
        foreach($primeImps as $imp){
            if(!self::isMainArea($imp->getImprvDetTypeCd())) {
                // It was found that condos had no DetCalcVal and should fall back to DetVal for accuracy
                if($imp->getDetCalcVal() == 0){
                    $detValCalcSum += $imp->getDetVal();
                } else {
                    $detValCalcSum += $imp->getDetCalcVal();
                }
            }
        }
        return $detValCalcSum;
    }


    /**
     * The unit price as of 2017 is
     * RCN/SF of subj high val main area
     * NOTE: found that the samples weren't using the simple unit price of imp with 'T'
     * @param $propertyImps
     * @return float
     * @throws Exception
     */
    public static function calculateUnitPrice($propertyImps){

        if(sizeof($propertyImps) == 0){
            throw new Exception("No improvements found");
        }
        $primeImps = self::getPrimaryImprovements($propertyImps);
        if(sizeof($primeImps) == 0){
            throw new Exception("No primary improvements found");
        }
        $totalReplacementCost = 0; //RCN
        $mainSqft = 0;
        foreach($primeImps as $imp){
            /* @var ImprovementDetailClass $secImp */
            if(self::isMainArea($imp->getImprvDetTypeCd())){
                $totalReplacementCost += $imp->getDetCalcVal();
                $mainSqft += $imp->getDetArea();
            }
        }
        if($mainSqft == 0){
            throw new Exception("No Main Area in primary improvments");
        }
        $unitPrice = round(($totalReplacementCost / $mainSqft), 2);
        return $unitPrice;
    }

}