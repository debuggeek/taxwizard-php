<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 5/18/14
 * Time: 5:00 PM
 */

namespace tests;

include 'massreport.php';

class MassReportTest extends PHPUnit_Framework_TestCase {

    public function test()
    {
        genMassReport('710420');
    }
}
 