<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/2/16
 * Time: 12:30 PM
 */
namespace classes;

include_once 'propertyClass.php';

class compRun{

    /**
     * @var propertyClass $subject Subject property of run
     */
    private $subject;

    /**
     * @var propertyClass() Array of comps
     */
    private $comparables;

    function __construct($subj, $comps = NULL){
        $this->subject = $subj;
        $this->comparables = $comps;
    }
    
}