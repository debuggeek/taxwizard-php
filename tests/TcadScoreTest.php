<?php
include ('TestUtil.php');
include ('../library/propertyClass.php');
include ('../library/TcadScore.php');

use TaxWizard\TcadScore;
use TaxWizardTest\TestUtil;
use PHPUnit\Framework\TestCase;

class TcadScoreTest extends Testcase
{

    public function testCalculateTcadScore_FullMatch()
    {
        $subj = TestUtil::generateProperty();
        $comp = TestUtil::generateProperty();

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        // 2 Matching props should be 100
        $this->assertEquals(100, $tcadScore->getScore());
    }

    public function testCalculateTcadScore_ClassDiff()
    {
        $subj = TestUtil::generateProperty();
        $comp = TestUtil::generateProperty();
        $comp->setClassCode('XX');

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        // Class doesn't matter as of 2019
        $this->assertEquals(100, $tcadScore->getScore());
    }

    public function testCalculateTcadScore_ConditionDiff()
    {
        $subj = TestUtil::generateProperty();
        $comp = TestUtil::generateProperty();
        $comp->setCondition('B');

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        // Mismatch of condidtion should be -15 points as of 2019
        $this->assertEquals(85, $tcadScore->getScore());
    }

    public function testCalculateTcadScore_HoodDiff()
    {
        $subj = TestUtil::generateProperty();
        $comp = TestUtil::generateProperty();
        $comp->mNeighborhood = 'Different';

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        //Hood shouldn't change score
        $this->assertEquals(100, $tcadScore->getScore());
    }

    public function testCalculateTcadScore_SitusDiff()
    {
        $subj = TestUtil::generateProperty();
        $comp = TestUtil::generateProperty();
        $comp->situs_street = 'ELM';

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        // Situs diff doesn't matter as of 2019
        $this->assertEquals(100, $tcadScore->getScore());
    }

    public function testCalculateTcadScore_SubdivisionDiff()
    {
        $subj = TestUtil::generateProperty();
        $comp = TestUtil::generateProperty();
        $comp->setSubdivision('Different');

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        // Subdiv is a 5 point diff as of 2019
        $this->assertEquals(95, $tcadScore->getScore());
    }

    public function testCalculateTcadScore_StCodeDiff()
    {
        $subj = TestUtil::generateProperty();
        $comp = TestUtil::generateProperty();
        $comp->stateCode = 'B1';

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        // Doesn't matter as of 2019
        $this->assertEquals(100, $tcadScore->getScore());
    }

    public function testCalculateTcadScore_subClassDiff()
    {
        $subj = TestUtil::generateProperty();
        $comp = TestUtil::generateProperty();
        $subj->setSubClass('5+');
        $comp->setSubClass('5-');

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        $this->assertEquals(90, $tcadScore->getScore());

        $comp->setSubClass('6-');
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        $this->assertEquals(95, $tcadScore->getScore());
    }

    public function testCalculateTcadScore_EffYrDiff()
    {
        $subj = TestUtil::generateProperty();
        $comp = TestUtil::generateProperty();
        $comp->effectiveYearBuilt = 2012;

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        //Doesn't matter as of 2019
        $this->assertEquals(100, $tcadScore->getScore());

        $comp->effectiveYearBuilt = 2007;

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        //Doesn't matter as of 2019
        $this->assertEquals(100, $tcadScore->getScore());
    }

    public function testCalculateTcadScore_AreaDiff()
    {
        $subj = TestUtil::generateProperty();
        $comp = TestUtil::generateProperty();
        $subj->setLivingArea(3166);
        $comp->setLivingArea(3121);

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        $this->assertEquals(100, $tcadScore->getScore());

        $comp->setLivingArea(2840);

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        $this->assertEquals(95, $tcadScore->getScore());
    }

    public function testCalculateTcadScore_ActYear()
    {
        $subj = TestUtil::generateProperty();
        $comp = TestUtil::generateProperty();
        $subj->mYearBuilt = 1973;
        $comp->mYearBuilt = 1972;

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        $this->assertEquals(100, $tcadScore->getScore());

        $comp->mYearBuilt = 1968;

        $tcadScore = new TcadScore();
        $tcadScore->setScore($subj, $comp);

        var_dump($tcadScore);

        $this->assertEquals(97, $tcadScore->getScore());
    }
}