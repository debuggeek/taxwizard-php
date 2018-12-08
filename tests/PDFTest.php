<?php

include '../library/functions_pdf.php';

class PDFTest extends PHPUnit_Framework_TestCase {

    public function test_genMassReportPDF()
    {
        global $debug;
        $debug=false;

        ini_set("error_log", "/dev/stderr");

        $queryContext = new queryContext();
        $queryContext->subjPropId = 123456;
        $queryContext->traceComps = true;
        $queryContext->sqftPercent = 30;
        $prop_pdfs = generatePropMultiPDF($queryContext);
        $this->assertNotEmpty($prop_pdfs);

        var_dump($prop_pdfs);
    }
}
 