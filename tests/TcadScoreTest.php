<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 4/30/16
 * Time: 8:30 AM
 */
namespace {
    require ('../library/propertyClass.php');
}

namespace TaxWizard {
    require ('TestUtil.php');
    require ('../library/TcadScore.php');

    class TcadScoreTest extends \PHPUnit_Framework_TestCase
    {

        public function testCalculateTcadScore_FullMatch()
        {
            $subj = TestUtil::generateProperty();
            $comp = TestUtil::generateProperty();

            $tcadScore = new TcadScore();
            $tcadScore->setScore($subj, $comp);

            var_dump($tcadScore);

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

            $this->assertEquals(95, $tcadScore->getScore());
        }

        public function testCalculateTcadScore_ConditionDiff()
        {
            $subj = TestUtil::generateProperty();
            $comp = TestUtil::generateProperty();
            $comp->setCondition('B');

            $tcadScore = new TcadScore();
            $tcadScore->setScore($subj, $comp);

            var_dump($tcadScore);

            $this->assertEquals(95, $tcadScore->getScore());
        }

        public function testCalculateTcadScore_SitusDiff()
        {
            $subj = TestUtil::generateProperty();
            $comp = TestUtil::generateProperty();
            $comp->situs_street = 'ELM';

            $tcadScore = new TcadScore();
            $tcadScore->setScore($subj, $comp);

            var_dump($tcadScore);

            $this->assertEquals(98, $tcadScore->getScore());
        }

        public function testCalculateTcadScore_StCodeDiff()
        {
            $subj = TestUtil::generateProperty();
            $comp = TestUtil::generateProperty();
            $comp->stateCode = 'B1';

            $tcadScore = new TcadScore();
            $tcadScore->setScore($subj, $comp);

            var_dump($tcadScore);

            $this->assertEquals(95, $tcadScore->getScore());
        }

        public function testCalculateTcadScore_subClassDiff()
        {
            $subj = TestUtil::generateProperty();
            $comp = TestUtil::generateProperty();
            $comp->setSubClass('6+');

            $tcadScore = new TcadScore();
            $tcadScore->setScore($subj, $comp);

            var_dump($tcadScore);

            $this->assertEquals(70, $tcadScore->getScore());
        }

        public function testCalculateTcadScore_EffYrDiff()
        {
            $subj = TestUtil::generateProperty();
            $comp = TestUtil::generateProperty();
            $comp->effectiveYearBuilt = 2012;

            $tcadScore = new TcadScore();
            $tcadScore->setScore($subj, $comp);

            var_dump($tcadScore);

            $this->assertEquals(97, $tcadScore->getScore());

            $comp->effectiveYearBuilt = 2007;

            $tcadScore = new TcadScore();
            $tcadScore->setScore($subj, $comp);

            var_dump($tcadScore);

            $this->assertEquals(98, $tcadScore->getScore());
        }

        public function testCalculateTcadScore_AreaDiff()
        {
            $subj = TestUtil::generateProperty();
            $comp = TestUtil::generateProperty();
            $comp->setLivingArea(2700);

            $tcadScore = new TcadScore();
            $tcadScore->setScore($subj, $comp);

            var_dump($tcadScore);

            $this->assertEquals(96, $tcadScore->getScore());

            $comp->setLivingArea(2946);

            $tcadScore = new TcadScore();
            $tcadScore->setScore($subj, $comp);

            var_dump($tcadScore);

            $this->assertEquals(91.08, $tcadScore->getScore());
        }

        public function testCalculateTcadScore_ActYear()
        {
            $subj = TestUtil::generateProperty();
            $comp = TestUtil::generateProperty();
            $comp->mYearBuilt = 2012;

            $tcadScore = new TcadScore();
            $tcadScore->setScore($subj, $comp);

            var_dump($tcadScore);

            $this->assertEquals(90, $tcadScore->getScore());

            $comp->mYearBuilt = 2007;

            $tcadScore = new TcadScore();
            $tcadScore->setScore($subj, $comp);

            var_dump($tcadScore);

            $this->assertEquals(94, $tcadScore->getScore());
        }
    }
}