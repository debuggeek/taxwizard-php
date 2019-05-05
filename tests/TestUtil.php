<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/30/16
 * Time: 10:31 AM
 */

namespace TaxWizardTest;

class TestUtil
{

    /**
     * @return \propertyClass
     */
    public static function generateProperty(){
        $prop = new \propertyClass();
        
        $prop->setPropID( rand (10000, 99999));
        $prop->setClassCode('WW');
        $prop->setSubClass('4+');
        $prop->setCondition('A');
        $prop->mNeighborhood = 'A5000';
        $prop->setSubdivision('S05688');
        //Not used currently
        ///$prop->school
        $prop->situs_street="Main";
        $prop->stateCode = 'A1';
        $prop->setSubClass('4+');
        $prop->effectiveYearBuilt = '2005';
        $prop->mYearBuilt = '2005';
        $prop->setLivingArea('2500');

        return $prop;
    }
}