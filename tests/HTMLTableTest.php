<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/24/16
 * Time: 1:47 PM
 */

include_once "../library/HTMLTable.php";

class HTMLTableTest extends PHPUnit_Framework_TestCase
{

    public function testToHTML_noParse(){
        $htmlTable = new HTMLTable();

        $this->assertNotEmpty($htmlTable->toHTML());

        print $htmlTable->toHTML();
    }

    public function testToHTML(){
        $jsonData = file_get_contents('sample3.json');
        $htmlTable = new HTMLTable();
        $htmlTable->parseJson($jsonData);

        $this->assertNotEmpty($htmlTable->toHTML());

        print $htmlTable->toHTML();
    }

}
