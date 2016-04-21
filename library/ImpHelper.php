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
    /**
     * Walks the primary improvments on subject and comps and finds deltas
     * If the comp doesn't have the primary improvment then one is added as a placeholder for the delta
     * @param ImprovementDetailClass() $subjImps
     * @param ImprovementDetailClass() $compImps
     * @return ImprovementDetailClass()
     */
    public static function compareImpDetails_AddDelta($subjImps, $compImps){

        //Walk the primary improvements and if the comp has it then calculate delta
        foreach (self::getPrimaryImprovements($subjImps) as $subjImp){
            $found = false;
            /* @var $subjImp ImprovementDetailClass */
            foreach ($compImps as $compImp){
                /* @var $compImp ImprovementDetailClass */
                if($subjImp->getImprvDetTypeCd() == $compImp->getImprvDetTypeCd()){
                    $found = true;
                    $compImp->setAdjustmentDelta(self::getDelta($subjImp, $compImp));
                    break;
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
            if($compImp->getAdjustmentDelta() == NULL){
                if(ImpHelper::isMainArea($subjImp->getImprvDetTypeCd())){
                    $result = $compImp->getDetArea() * $compImp->getDetUnitprice();
                    $compImp->setAdjustmentDelta(round($result));
                } else {
                    //negative because we get to count against subject
                    $compValue = $compImp->getDetArea() * $compImp->getDetUnitprice();
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
     * @return int
     */
    public static function getUniqueImpIds($propertyImps){
        $impDetImprIdList = self::getAllImpIds($propertyImps);
        return array_unique($impDetImprIdList);
    }

    /**
     * Returns the improvement id used for the primary improvement
     * @param array ImprovementDetailClass $propertyImps
     * @return null|string
     */
    public static function getPrimaryImpId($propertyImps){
        foreach ($propertyImps as $improv) {
            /* @var $improv ImprovementDetailClass */
            if($improv->isDetUseUnitPrice()){
                return $improv->getImprvId();
            }
        }    
        return null;
    }

    /**
     * Finds the number of primary improvements on a property
     * @param $properImps
     * @return ImprovementDetailClass[]
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
     * Finds all the non-primary improvements
     * @param $propertyImps
     * @return array
     */
    public static function getSecondaryImprovements($propertyImps){
        $primaryImpId = self::getPrimaryImpId($propertyImps);
        $secondaryImpr = array();
        foreach ($propertyImps as $improv) {
            /* @var $improv ImprovementDetailClass */
            if($primaryImpId !== $improv->getImprvId()){
                $secondaryImpr[] = $improv;
            }
        }
        return $secondaryImpr;    
    }

    public static function getSecondaryImprovementsValue($propertyImps){
        $secImpsVal = 0;
        $secImps = self::getSecondaryImprovements($propertyImps);
        foreach($secImps as $secImp){
            /* @var ImprovementDetailClass $secImp */
            $secImpsVal +=  $secImp->getImprvVal();
        }
        return $secImpsVal;
    }

    /**
     * Finds the first improvement with a given code
     * @param $propertyImps
     * @param $impDetCode
     * @return ImprovementDetailClass|null
     */
    public static function getImprovObjByCode($propertyImps, $impDetCode){
        foreach ($propertyImps as $improv) {
            /* @var $improv ImprovementDetailClass */
            if ($impDetCode == $improv->getImprvDetTypeCd()) {
                return $improv;
            }
        }
        return null;
    }

    /**
     * @param ImprovementDetailClass $subjImp
     * @param ImprovementDetailClass $compImp
     * @return int
     */
    private static function getDelta($subjImp, $compImp){
        if(ImpHelper::isMainArea($subjImp->getImprvDetTypeCd())){
            if($compImp->getDetArea() == NULL || $compImp->getDetArea() == "NONE"){
                //If the comp doesn't have the element then we use the subj unit price
                $result = $subjImp->getDetArea() * $subjImp->getDetUnitprice();
            } else {
                $areaDiff = $subjImp->getDetArea() - $compImp->getDetArea();
                $result = $areaDiff * $compImp->getDetUnitprice();
            }
            return round($result);
        } else {
            $subjValue = $subjImp->getDetArea() * $subjImp->getDetUnitprice();
            $compValue = $compImp->getDetArea() * $compImp->getDetUnitprice();
            return round($subjValue - $compValue);
        }
    }

    /**
     * @param string $improvementCode
     * @return bool
     */
    private static function isMainArea($improvementCode){
        $allowablema = array("1/2","1ST","2ND","3RD","4TH","5TH","ADDL","ATRM","BELOW",
                                "CONC","DOWN","FBSMT","LOBBY","MEZZ","PBSMT","RSBLW","RSDN");
        return in_array($improvementCode, $allowablema);
    }
}