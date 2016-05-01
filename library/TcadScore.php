<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/30/16
 * Time: 7:52 AM
 */

namespace TaxWizard;

class TcadScore
{
    
    private $classPoints = 0;

    /**
     * @var int
     */
    private $conditionPoints = 0;

    private $hoodPoints = 0;
    
    private $schoolPoints = 0;
    
    private $streetPoints = 0;
    
    private $stateCodePoints = 0;
    
    private $subClassPoints = 0;
    
    private $effectiveYearBuiltPoints = 0;
    
    private $livingAreaPoints = 0;
    
    private $acutalYearBuiltPoints = 0;

    /**
     * @return float
     */
    public function getScore(){
        return $this->classPoints
                + $this->conditionPoints
                + $this->hoodPoints
                + $this->schoolPoints
                + $this->streetPoints
                + $this->stateCodePoints
                + $this->subClassPoints
                + $this->effectiveYearBuiltPoints
                + $this->livingAreaPoints
                + $this->acutalYearBuiltPoints;
    }

    /**
     * @param \propertyClass $subjProp
     * @param \propertyClass $compProp
     * @return TcadScore
     */
    public function setScore($subjProp, $compProp){
        $this->classPoints = $this->calculateClassPoints($subjProp->getClassCode(), $compProp->getClassCode());
        $this->conditionPoints = $this->calculateConditionPoints($subjProp->getCondition(), $compProp->getCondition());
        $this->hoodPoints = $this->calculateHoodPoints($subjProp->mNeighborhood, $compProp->mNeighborhood);
        //No value for school as of 2016 scoring
        $this->schoolPoints = 0;
        $this->streetPoints = $this->calculateStreetPoints($subjProp->getSitus(), $compProp->getSitus());
        $this->stateCodePoints = $this->calculateStCodePoints($subjProp->stateCode, $compProp->stateCode);
        $this->subClassPoints = $this->calculateSubClassPoints($subjProp->getSubClass(), $compProp->getSubClass());
        $this->effectiveYearBuiltPoints = $this->calculateEffectiveYearBuiltPoints($subjProp->effectiveYearBuilt, $compProp->effectiveYearBuilt);
        $this->livingAreaPoints = $this->calculateLivingAreaPoints($subjProp->getLivingArea(), $compProp->getLivingArea());
        $this->acutalYearBuiltPoints = $this->calculateActYearBuiltPoints($subjProp->getYearBuilt(), $compProp->getYearBuilt());
        
        return $this;
    }
    
    private function calculateClassPoints($subjClassAdj, $compClassAdj){
        if($subjClassAdj === $compClassAdj){
            return 5;
        }      
        return 0;
    }
    
    private function calculateConditionPoints($subjCond, $compCond){
        if($subjCond === $compCond){
            return 5;
        }
        return 0;
    }

    private function calculateHoodPoints($subjHood, $compHood){
        return 0;
    }

    private function calculateStreetPoints($subjSitus, $compSitus){
        if(strcasecmp($subjSitus,$compSitus) == 0){
            return 2;
        }
        return 0;
    }

    private function calculateStCodePoints($subjStCode, $compStCode){
        if($subjStCode === $compStCode){
            return 5;
        }
        return 0;
    }

    private function calculateSubClassPoints($subjSubClass, $compSubClass){
        $most = 35;

        $subClassRanges = array('2-','2','2+','3-','3','3+','4-','4','4+','5-','5','5+','6-','6','6+','7-','7','7+','8-','8','8+');
        $subjPos = array_search($subjSubClass, $subClassRanges);
        if($subjPos === false){
           throw new \Exception("fallsInsideClassRange: Couldn't find ". $subjSubClass . " in " . $subClassRanges . " for subject  SubClassAdj NOT in range");
        }
        $compPos = array_search($compSubClass,$subClassRanges);
        if($compPos === false){
            throw new \Exception("fallsInsideClassRange: Couldn't find ". $compSubClass . " in " . $subClassRanges . " for comp assuming SubClassAdj NOT in range");
        }

        $diff = abs($subjPos-$compPos);
        //5 pts per difference
        $pointsOff = $diff * 5;
        $value = $most - $pointsOff;
        if($value < 0){
            $value = 0;
        }
        return $value;
    }

    private function calculateEffectiveYearBuiltPoints($subjEffYr, $compEffYr){
        $most = 3;
        $yrDiff = abs($subjEffYr-$compEffYr);
        $pointsOff = $yrDiff * 1;
        $value = $most - $pointsOff;
        if($value < 0){
            $value = 0;
        }
        return $value;
    }

    private function calculateLivingAreaPoints($subjLivArea, $compLivArea){
        $most = 35;
        $diff = abs($subjLivArea-$compLivArea);
        //5 pts per 250Sqft difference
        $pointsOff = ($diff/250) * 5;
        $value = $most - $pointsOff;
        if($value < 0){
            $value = 0;
        }
        return $value;
    }

    private function calculateActYearBuiltPoints($subjActYear, $compActYear){
        $most = 10;
        $yrDiff = abs($subjActYear-$compActYear);
        $pointsOff = $yrDiff * 3;
        $value = $most - $pointsOff;
        if($value < 0){
            $value = 0;
        }
        return $value;
    }
}