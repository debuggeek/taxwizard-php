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

        $this->assertNotEmpty($htmlTable->toHTML(false));

        print $htmlTable->toHTML(false);
    }

    public function testToHTML(){
        $jsonData = file_get_contents('sample3.json');
        $htmlTable = new HTMLTable();
        $htmlTable->parseJson($jsonData);

        $this->assertNotEmpty($htmlTable->toHTML(false));

        print $htmlTable->toHTML(false);
    }

    public function testToHTML_Equity(){
        $jsonData = file_get_contents('resources/samples/fulltable_equity.json');
        $htmlTable = new HTMLTable();
        $htmlTable->parseJson($jsonData);

        $this->assertNotEmpty($htmlTable->toHTML(true));

        print $htmlTable->toHTML(true);
    }

}
