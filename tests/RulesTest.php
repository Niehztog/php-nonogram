<?php

use Nonogram\Cell\AnyCell;

class RulesTest extends AbstractTest
{

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
        list($solver, $labels, $fieldOverride) = $this->initSolverForRule($className, $labelArr, $rowString);
        /* @var $solver \Nonogram\Solver\SolverJ54 */
        /* @var $labels \Nonogram\Label\Label */

        $field = $solver->solve($labels, $fieldOverride);
        foreach ($expected as $assumption) {
            $this->assertEquals($assumption['val'], $field[0][$assumption['pos']]->getType(), $className . ': at pos ' . $assumption['pos']);
        }
    }

    /**
     * @return array
     */
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
        list($solver, $labels, $fieldOverride) = $this->initSolverForRule($className, $labelArr, $rowString);
        /* @var $solver \Nonogram\Solver\SolverJ54 */
        /* @var $labels \Nonogram\Label\Label */

        $runRangeOverride = $this->runRangeFactory->getForLabels($labels);
        $ranges = &$runRangeOverride->getRangesForRow(1);
        $ranges = $injectRanges;

        $solver->solve($labels, $fieldOverride, $runRangeOverride);
        $this->assertEquals($expectedRanges, $ranges, $className);
    }

    /**
     * @return array
     */
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
            array('MyRule_6', array(1,1,2), 'UUEBEUUEBEUBUUU', array(1=>array('s'=>0,'e'=>3), 2=>array('s'=>3,'e'=>11), 3=>array('s'=>10,'e'=>14)), array(1=>array('s'=>3,'e'=>3), 2=>array('s'=>8,'e'=>8), 3=>array('s'=>10,'e'=>14)))
        );
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
        $field = array(0 => $this->convertRowRawToActual($rowString));

        $labels->setCol(array_fill(0, count($field[0]), array()));
        $className = '\\Nonogram\\Solver\\Rule\\' . $className;
        $rule = new $className($this->cellFactory);
        $solver = new \Nonogram\Solver\SolverJ54($this->cellFactory, $this->runRangeFactory);
        $solver->attachRule($rule);
        return array($solver, $labels, $field);
    }

}
