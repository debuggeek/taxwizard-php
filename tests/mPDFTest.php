<?php

/**
 * Created by PhpStorm.
 * User: norrick
 * Date: 4/25/16
 * Time: 6:49 PM
 */
class mPDFTest extends PHPUnit_Framework_TestCase
{

    public function testBasic(){
        $content = file_get_contents('constructedHtml.html');

        $mpdf = new mPDF();
        $stylesheet = file_get_contents('../default.css');
        // The parameter 1 tells that this is css/style only and no body/html/text
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($content);
        $mpdf->Output('/Users/norrick/Downloads/mPDFexample.pdf','F');
    }

    public function testPDFGeneration(){
        $content = file_get_contents('samplePhpHtmlOutput.html');

        $mpdf=new mPDF('c','A4-L');//,"","","1","1","1","1");
        $mpdf->SetMargins(1,1,1);
//        $mpdf->SetDisplayMode('fullwidth');
//        $mpdf->shrink_tables_to_fit = 2;
        $stylesheet = file_get_contents('../default_pdf.css');

        // The parameter 1 tells that this is css/style only and no body/html/text
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($content,2);
        $mpdf->Output("/Users/Nick/Downloads/mdpfTest.pdf",'F');

    }
}
