<?php

namespace Nonogram\Solver;

use Nonogram\Label\Label;
use Nonogram\Solver\Rule\AbstractRuleJ54;
use Nonogram\Solver\RunRange\RunRange;

/**
 * Class Solver
 *
 * basically a collection of solving strategies
 *
 * @package Nonogram\Solver
 */
class SolverJ54 implements AnySolver
{

    private \Nonogram\Label\Label $labels;

    private array $field;

    /**
     * @var \Nonogram\Solver\Rule\AbstractRuleJ54[]
     */
    private array $rules = [];

    private \Nonogram\Solver\RunRange\RunRange $runRanges;

    /**
     * counts how many updates come from each rule
     */
    private array $solvingStatistics = [];

    /**
     * Time in microseconds it took to solve the puzzle
     */
    private float $lastSolvingTime = 0;

    private \Nonogram\Cell\Factory $cellFactory;

    private \Nonogram\Solver\RunRange\RunRangeFactory $runRangeFactory;

    private array $finishedRows = [];

    private array $finishedCols = [];

    /**
     * SolverJ54 constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     * @param \Nonogram\Solver\RunRange\RunRangeFactory $runRangeFactory
     */
    public function __construct(
        \Nonogram\Cell\Factory $cellFactory,
        \Nonogram\Solver\RunRange\RunRangeFactory $runRangeFactory
    )
    {
        $this->cellFactory = $cellFactory;
        $this->runRangeFactory = $runRangeFactory;
    }

    /**
     * Init method
     * @param Label $labels
     * @param array $fieldOverride option to inject a pre-defined state, only useful for unittests
     * @param \Nonogram\Solver\RunRange\RunRange|null $runRangeOverride option to inject a pre-defined state, only useful for unittests
     */
    private function init(
        \Nonogram\Label\Label $labels,
        array $fieldOverride = [],
        RunRange $runRangeOverride = null
    )
    {
        $this->finishedRows = [];
        $this->finishedCols = [];
        $this->solvingStatistics = [];
        $this->labels = $labels;
        $this->initField($fieldOverride);
        $this->initRunRanges($runRangeOverride);
    }

    /**
     * @param Rule\AbstractRuleJ54 $rule
     */
    public function attachRule(\Nonogram\Solver\Rule\AbstractRuleJ54 $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * Inits the field with all cells set to "unknown"
     * @param array $fieldOverride option to inject a pre-defined state, only useful for unittests
     */
    private function initField(array $fieldOverride = [])
    {
        if(!empty($fieldOverride)) {
            $this->field = $fieldOverride;
            return;
        }

        $sizeX = $this->labels->getSizeX();
        $sizeY = $this->labels->getSizeY();

        $this->field = [];

        for ($indexX = 0;$indexX < $sizeX; $indexX++) {
            for ($indexY = 0;$indexY < $sizeY; $indexY++) {
                $this->field[$indexY][$indexX] = $this->cellFactory->getUnknown();
            }
        }
    }

    /**
     * @param \Nonogram\Solver\RunRange\RunRange|null $runRangeOverride option to inject a pre-defined state, only useful for unittests
     */
    private function initRunRanges(RunRange $runRangeOverride = null)
    {
        if(null !== $runRangeOverride) {
            $this->runRanges = $runRangeOverride;
            return;
        }

        $this->runRanges = $this->runRangeFactory->getForLabels($this->labels);
    }

    /**
     * @param Label $labels
     * @param array $fieldOverride option to inject a pre-defined state, only useful for unittests
     * @param \Nonogram\Solver\RunRange\RunRange|null $runRangeOverride  option to inject a pre-defined state, only useful for unittests
     * @return array
     */
    public function solve(
        \Nonogram\Label\Label $labels,
        array $fieldOverride = [],
        RunRange $runRangeOverride = null
    )
    {
        $timeStart = microtime(true);
        $this->init($labels, $fieldOverride, $runRangeOverride);
        if($labels instanceof \Nonogram\Label\LabelColored && $labels->getColorCount() > 1) {
            return $this->field;
        }
        if($labels->hasHiddenCounts()) {
            return $this->field;
        }
        $iterations = 0;

        do {
            $updateCounter = 0;

            //iterate over all rows
            foreach ($this->field as $rowNum => &$row) {
                if(!empty($this->finishedRows) && in_array($rowNum, $this->finishedRows)) {
                    continue;
                }
                $blackRuns = $this->labels->getLabelsForRow($rowNum + 1);
                $r = &$this->runRanges->getRangesForRow($rowNum + 1);
                foreach ($this->rules as $rule) {
                    if($rule::RESULT_LINE_SOLVED === $rule->apply($row, $blackRuns, $r)) {
                        $this->finishedRows[] = $rowNum;
                    }
                    $updateCounter += $this->processUpdateCounter($rule);
                }
            }

            if (count($this->field) === 1) {
                return $this->field;
            }

            //iterate over all columns
            for ($colNum = 0; $colNum < $this->labels->getSizeX(); $colNum++) {
                if(!empty($this->finishedCols) && in_array($colNum, $this->finishedCols)) {
                    continue;
                }
                //compose a new sequence of the column
                $sequence = [];
                for ($rowNum = 0; $rowNum < $this->labels->getSizeY(); $rowNum++) {
                    $sequence[$rowNum] = &$this->field[$rowNum][$colNum];
                }
                $blackRuns = $this->labels->getLabelsForColumn($colNum + 1);
                $r = &$this->runRanges->getRangesForColumn($colNum + 1);
                foreach ($this->rules as $rule) {
                    if($rule::RESULT_LINE_SOLVED === $rule->apply($sequence, $blackRuns, $r)) {
                        $this->finishedCols[] = $colNum;
                    }
                    $updateCounter += $this->processUpdateCounter($rule);
                }
            }
            $iterations++;
        } while ($updateCounter > 0);

        $this->lastSolvingTime = microtime(true) - $timeStart;
        $this->solvingStatistics['Iterations'] = $iterations;
        $this->solvingStatistics['Solving time'] = $this->lastSolvingTime;
        return $this->field;
    }

    private function processUpdateCounter(AbstractRuleJ54 $rule)
    {
        $updates = $rule->getUpdateCounter();
        $ruleClass = get_class($rule);
        $key = substr($ruleClass, strrpos($ruleClass, '\\') + 1);
        if (!isset($this->solvingStatistics[$key])) {
            $this->solvingStatistics[$key] = 0;
        }
        $this->solvingStatistics[$key] += $updates;
        return $updates;
    }

    /**
     * Getter for property "ruleActionCounter"
     */
    public function getSolvingStatistics(): array
    {
        return $this->solvingStatistics;
    }
}
