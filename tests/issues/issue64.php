<?php
include ('../../library/propertyClass.php');
include ('../../library/TcadScore.php');

include_once "../../library/functions.php";

use TaxWizard\TcadScore;
use PHPUnit\Framework\TestCase;

class Issue64 extends Testcase
{
    /**
     * @throws Exception
     */
    public function testTcadScores()
    {
        global $debug;

        $debug = false;
        $subject = getProperty(121352);
        $comp1 = getProperty(121437);

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subject, $comp1);

        var_dump($tcadScore);
        $this->assertEquals(90, $tcadScore->getScore());

        $comp2 = getProperty(221317);

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subject, $comp2);

        var_dump($tcadScore);
        $this->assertEquals(77, $tcadScore->getScore());

        $comp3 = getProperty(224516);

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subject, $comp3);

        var_dump($tcadScore);
        $this->assertEquals(75, $tcadScore->getScore());

        $comp4 = getProperty(221271);

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subject, $comp4);

        var_dump($tcadScore);
        $this->assertEquals(82, $tcadScore->getScore());
    }
}