<?php

/**
 * Created by PhpStorm.
 * User: norrick
 * Date: 4/25/16
 * Time: 7:44 PM
 */
//require_once('../html2pdf-4.5.1/html2pdf.class.php');

class html2pdfTest extends PHPUnit_Framework_TestCase
{
    public function testBasic(){
        $content = "
            <page>
                <h1>test</h1>
                <br>
                Hello <b>World</b>
                Thanks <a href='http://html2pdf.fr/'>HTML2PDF</a>.<br>
            </page>";

        $html2pdf = new HTML2PDF('P','A4','en');
        $html2pdf->WriteHTML($content);
        $html2pdf->Output('/Users/norrick/Downloads/example.pdf','F');
    }

    public function testSample(){
        $content = file_get_contents('samplePhpHtmlOutput.html');

        $html2pdf = new HTML2PDF('L','A4','en');
        $html2pdf->WriteHTML($content);
        $html2pdf->Output('/Users/norrick/Downloads/example.pdf','F');
    }
}
