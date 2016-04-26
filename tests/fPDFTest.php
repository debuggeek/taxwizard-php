<?php

use Anouar\Fpdf\Fpdf;

class fPDFTest extends PHPUnit_Framework_TestCase
{
    public function testPDFGeneration()
    {
        $pdf = new PDF_HTML();

        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(true, 15);

        $pdf->AddPage();
        $pdf->WriteHTML(file_get_contents('samplePhpHtmlOutput.html'));

        file_put_contents('/Users/nick/Downloads/fPDFTest.pdf', $pdf->Output());
    }

}