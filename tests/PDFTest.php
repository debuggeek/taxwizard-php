<?php

include '../library/functions_pdf.php';

class PDFTest extends PHPUnit_Framework_TestCase {

    public function test_genMassReportPDF()
    {
        $prop_pdfs = generatePropMultiPDF(105290);
    }
}
 