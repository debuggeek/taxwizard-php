<?php
require_once __DIR__ . '/vendor/autoload.php';
//include namespace

use DebugGeek\TaxWizard;

/**
 * Autload project files
 */
function __autoload($class_name) {
    if(file_exists($class_name . '.php')) {
        require_once($class_name . '.php');
    } else {
        throw new Exception("Unable to load $class_name.");
    }
}
