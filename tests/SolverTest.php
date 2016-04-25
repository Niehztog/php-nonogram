<?php

use Nonogram\Cell\AnyCell;

class SolverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Tests that the solver doesn't come to false conclusions
     *
     * Verifies only that all cells marked by the solver correspond to the actual solution
     * Does *not* check that the solver solved all cells (unknown cells are skipped)
     * @test
     */
    public function testSolverAll()
    {
        $fileNameSolved = 'alona1.dat';
        $fileNameUnsolved = 'alona1.yml';
        $rootDir = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;
        $filePathFull = $rootDir . 'data' . DIRECTORY_SEPARATOR . 'Levels' . DIRECTORY_SEPARATOR;

        $gridSolved = $this->loadFile($filePathFull.$fileNameSolved);
        $gridUnsolved = $this->loadFile($filePathFull.$fileNameUnsolved);

        $cellsSolved = $gridSolved->getCells();
        $cellsUnsolved = $gridUnsolved->getCells();

        foreach ($cellsSolved as $i => $row) {
            foreach ($row as $j => $cell) {
                if ($cellsUnsolved[$i][$j]->getType() === AnyCell::TYPE_UNKNOWN) {
                    continue;
                }
                $this->assertEquals($cellsSolved[$i][$j]->getType(), $cellsUnsolved[$i][$j]->getType());
            }
        }
    }

    /**
     * Tests a single determination rule (rule which unveils cells)
     *
     * @param $className
     * @param array $labelArr
     * @param $rowString
     * @param $expectedValue
     * @param $expectedPosition
     *
     * @dataProvider ruleDeterminationDataProvider
     */
    public function testRuleDetermination($className, array $labelArr, $rowString, array $expected)
    {
        list($solver, $labels) = $this->initSolverForRule($className, $labelArr, $rowString);
        /* @var $solver \Nonogram\Solver\SolverJ54 */
        /* @var $labels \Nonogram\Label\Label */

        $field = $solver->solve($labels);
        foreach ($expected as $assumption) {
            $this->assertEquals($assumption['val'], $field[0][$assumption['pos']]->getType(), 'at pos ' . $assumption['pos']);
        }
    }

    public function ruleDeterminationDataProvider()
    {
        return array(
            array('RuleJ54_1_3', array(2,1,1,3), 'UUUUUUUBUBUU', array(array('val'=>AnyCell::TYPE_EMPTY, 'pos'=>6))),
            array('RuleJ54_1_4', array(1,3,1), 'UUBUBBUUUU', array(array('val'=>AnyCell::TYPE_EMPTY, 'pos'=>3))),
            array('RuleJ54_1_5', array(3,4), 'UUUEUBUUUUUUU', array(array('val'=>AnyCell::TYPE_BOX, 'pos'=>6))),
            array('RuleJ54_1_5', array(1,2,2,3), 'UUUUUBBUUUUUUU', array(array('val'=>AnyCell::TYPE_EMPTY, 'pos'=>4)
                                                                        ,array('val'=>AnyCell::TYPE_EMPTY, 'pos'=>7))),
            array('RuleJ54_3_1', array(1,4,3), 'UUUUBUBUUUUUU', array(array('val'=>AnyCell::TYPE_BOX, 'pos'=>5))),
            array('RuleJ54_3_2', array(3), 'UUEUEEUUBEUEUBUEUU', array(array('val'=>AnyCell::TYPE_EMPTY, 'pos'=>10))),
            //array('RuleJ54_3_3_1', array(4), 'UUUUUBUUUUUU', array(array('val'=>AnyCell::TYPE_EMPTY, 'pos'=>10))),
        );
    }

    /**
     * Tests a single run refinement rule (rule which refines black run ranges for later rule application)
     *
     * @param $className
     * @param array $labelArr
     * @param $rowString
     * @param $expectedValue
     * @param $expectedPosition
     *
     * @dataProvider ruleRefinementDataProvider
     */
    public function testRuleRefinement($className, array $labelArr, $rowString, array $injectRanges,array $expectedRanges)
    {
        list($solver, $labels) = $this->initSolverForRule($className, $labelArr, $rowString);
        /* @var $solver \Nonogram\Solver\SolverJ54 */
        /* @var $labels \Nonogram\Label\Label */

        $rangeProvider = $solver->getRunRanges($labels);
        $ranges = &$rangeProvider->getRangesForRow(1);
        $ranges = $injectRanges;

        $solver->solve($labels);
        $this->assertEquals($expectedRanges, $ranges);
    }

    public function ruleRefinementDataProvider()
    {
        return array(
            array('RuleJ54_2_1', array(3,3), 'UUUUUUUUUU', array(1=>array('s'=>0,'e'=>5),2=>array('s'=>0,'e'=>9)), array(1=>array('s'=>0,'e'=>5),2=>array('s'=>4,'e'=>9))),
            array('RuleJ54_2_2', array(1,4), 'BUUUUUUUUU', array(1=>array('s'=>0,'e'=>0),2=>array('s'=>1,'e'=>9)), array(1=>array('s'=>0,'e'=>0),2=>array('s'=>2,'e'=>9))),
            array('RuleJ54_2_3', array(3,2,1), 'UUUUBBBUUUUBUU', array(1=>array('s'=>0,'e'=>8),2=>array('s'=>4,'e'=>11),3=>array('s'=>7,'e'=>13)), array(1=>array('s'=>0,'e'=>8),2=>array('s'=>8,'e'=>11),3=>array('s'=>7,'e'=>13))),
            array('RuleJ54_3_1', array(1,4,3), 'UUUUBUBUUUUUU', array(1=>array('s'=>0,'e'=>3),2=>array('s'=>2,'e'=>8),3=>array('s'=>7,'e'=>12)), array(1=>array('s'=>0,'e'=>3),2=>array('s'=>3,'e'=>7),3=>array('s'=>7,'e'=>12))),
            array('RuleJ54_3_2', array(3), 'UUEUEEUUBEUEUBUEUU', array(1=>array('s'=>1,'e'=>16)), array(1=>array('s'=>6,'e'=>14))),
            array('RuleJ54_3_3_1', array(1,4,1), 'UUUUUBUUUUUU', array(1=>array('s'=>0,'e'=>4),2=>array('s'=>5,'e'=>10),3=>array('s'=>7,'e'=>11)), array(1=>array('s'=>0,'e'=>3),2=>array('s'=>5,'e'=>8),3=>array('s'=>10,'e'=>11))),
            array('RuleJ54_3_3_3', array(1,4,1), 'UUUUUBUUUBUU', array(1=>array('s'=>0,'e'=>2),2=>array('s'=>3,'e'=>10),3=>array('s'=>9,'e'=>11)), array(1=>array('s'=>0,'e'=>2),2=>array('s'=>3,'e'=>7),3=>array('s'=>9,'e'=>11))),
            array('RuleJ54_3_3_3', array(1,4,1), 'UUBUUUBUUUUU', array(1=>array('s'=>0,'e'=>2),2=>array('s'=>1,'e'=>8),3=>array('s'=>9,'e'=>11)), array(1=>array('s'=>0,'e'=>2),2=>array('s'=>4,'e'=>8),3=>array('s'=>9,'e'=>11))),
            array('MyRule_4', array(1,2,1,1,1,1), 'UUEUUUUUEUUEBEB', array(1=>array('s'=>0,'e'=>3),2=>array('s'=>3,'e'=>6),3=>array('s'=>5,'e'=>7),4=>array('s'=>7,'e'=>10),5=>array('s'=>12,'e'=>12),6=>array('s'=>14,'e'=>14)), array(1=>array('s'=>0,'e'=>1),2=>array('s'=>3,'e'=>5),3=>array('s'=>6,'e'=>7),4=>array('s'=>9,'e'=>10),5=>array('s'=>12,'e'=>12),6=>array('s'=>14,'e'=>14))),
        );
    }

    /**
     * Loads a grid layout from file
     *
     * @param $filePathFull
     */
    private function loadFile($filePathFull)
    {
        $factory = new \Nonogram\Grid\Factory($filePathFull);
        $grid = $factory->getFromFile($filePathFull);
        return $grid;
    }

    /**
     * @param $className
     * @param array $labelArr
     * @param $rowString
     * @return array
     */
    private function initSolverForRule($className, array $labelArr, $rowString)
    {
        $labels = new \Nonogram\Label\Label();
        $labels->setRow(array($labelArr));
        $f = new \Nonogram\Cell\Factory();
        $field = array(array());
        for ($i = 0; $i < strlen($rowString); $i++) {
            switch ($rowString{$i}) {
                case 'U':
                    $cell = $f->getUnknown();
                    break;
                case 'B':
                    $cell = $f->getBox();
                    break;
                case 'E':
                    $cell = $f->getEmpty();
                    break;
            }
            $field[0][] = $cell;
        }

        $labels->setCol(array_fill(0, count($field[0]), array()));

        $className = '\\Nonogram\\Solver\\Rule\\' . $className;
        $rule = new $className();
        $solver = new \Nonogram\Solver\SolverJ54();
        $solver->setField($field);
        $solver->attachRule($rule);
        return array($solver, $labels);
    }
}
