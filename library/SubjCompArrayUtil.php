<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 12/2/18
 * Time: 10:14 PM
 */

/**
 * @param propertyClass[] $subjcomp
 * @return int
 */
function calcLowVal($subjcomp) : int
{
    $result = 0;
    $compCount = count($subjcomp) -1;
    //don't include subj
    for($i=1;$i <= $compCount; $i++){
        //Get value
        $value = $subjcomp[$i]->getIndicatedVal(false);
        //Start with first as lowest
        if($i==1){
            $result = $value;
        } else{
            if($value < $result){
                $result = $value;
            }
        }
    }
    return $result;
}

/**
 * @param propertyClass[] $subjcomp
 * @return int
 */
function calcHighVal($subjcomp) : int
{
    $result = 0;
    $compCount = count($subjcomp) -1;
    //don't include subj
    for($i=1;$i <= $compCount; $i++){
        //Get value
        $value = $subjcomp[$i]->getIndicatedVal(false);
        //Start with first as lowest
        if($i==1){
            $result = $value;
        } else{
            if($value > $result){
                $result = $value;
            }
        }
    }
    return $result;
}