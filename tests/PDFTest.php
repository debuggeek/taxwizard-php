<?php

include '../library/functions_pdf.php';

class PDFTest extends PHPUnit_Framework_TestCase {

    public function test_genMassReportPDF()
    {
        $queryContext = new queryContext();
        $queryContext->subjPropId = 105290;
        $prop_pdfs = generatePropMultiPDF($queryContext);
        $this->assertNotEmpty($prop_pdfs);
    }
}
 