<?php

namespace Nonogram\Solver;

use Nonogram\Label\Label;
use Nonogram\Solver\Rule\AbstractRuleJ54;

/**
 * Class Solver
 *
 * basically a collection of solving strategies
 *
 * @package Nonogram\Solver
 */
class SolverJ54 implements AnySolver
{
    /**
     * @var \Nonogram\Label\Label
     */
    private $labels;

    /**
     * @var array
     */
    private $field;

    /**
     * @var \Nonogram\Solver\Rule\AbstractRuleJ54[]
     */
    private $rules = array();

    /**
     * @var RunRange
     */
    private $runRanges;

    /**
     * counts how many updates come from each rule
     * @var array
     */
    private $ruleActionCounter = array();

    /**
     * Init method
     * @param \Nonogram\Label\Label $labels
     */
    private function init(\Nonogram\Label\Label $labels)
    {
        $this->labels = $labels;
        foreach($this->rules as $rule) {
            $rule->setLabels($labels);
        }
        $this->initField();
        $this->initRunRanges();
    }

    /**
     * @param Rule\AbstractRuleJ54 $rule
     */
    public function attachRule(\Nonogram\Solver\Rule\AbstractRuleJ54 $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * Setter for property "field" (injection required for unittests)
     * @param array $field
     */
    public function setField(array $field)
    {
        $this->field = $field;
    }

    /**
     * Getter for run ranges, only interesting for unittests
     *
     * @param Label|null $labels (optional)
     * @return RunRange
     */
    public function getRunRanges(\Nonogram\Label\Label $labels = null)
    {
        if(empty($this->runRanges) && !empty($this->field)) {
            $this->initRunRanges($labels);
        }
        return $this->runRanges;
    }

    /**
     * Inits the field with all cells set to "unknown"
     */
    private function initField()
    {
        if(!empty($this->field)) {
            return;
        }

        $sizeX = $this->labels->getSizeX();
        $sizeY = $this->labels->getSizeY();

        $f = new \Nonogram\Cell\Factory();
        $this->field = array();

        for ($indexX = 0;$indexX < $sizeX; $indexX++) {
            for ($indexY = 0;$indexY < $sizeY; $indexY++) {
                $this->field[$indexY][$indexX] = $f->getUnknown();
            }
        }
    }

    /**
     * @param Label|null $labels (optional)
     */
    private function initRunRanges(\Nonogram\Label\Label $labels = null)
    {
        if(!empty($this->runRanges)) {
            return;
        }

        $this->runRanges = new RunRange(empty($this->labels) && null !== $labels ? $labels : $this->labels);
    }

    /**
     * @param \Nonogram\Label\Label $labels
     * @return array
     */
    public function solve(\Nonogram\Label\Label $labels)
    {
        $this->init($labels);
        
        do {
            $updateCounter = 0;

            //iterate over all rows
            foreach ($this->field as $rowNum => &$row) {
                $blackRuns = $this->labels->getLabelsForRow($rowNum + 1);
                $r = &$this->runRanges->getRangesForRow($rowNum + 1);
                foreach ($this->rules as $rule) {
                    $rule->apply($row, $blackRuns, $r);
                    $updateCounter += $this->processUpdateCounter($rule);
                }
            }

            if (count($this->field) === 1) {
                return $this->field;
            }

            //iterate over all columns
            for ($colNum = 0; $colNum < $this->labels->getSizeX(); $colNum++) {
                //compose a new sequence of the column
                $sequence = array();
                for ($rowNum = 0; $rowNum < $this->labels->getSizeY(); $rowNum++) {
                    $sequence[$rowNum] = &$this->field[$rowNum][$colNum];
                }
                $blackRuns = $this->labels->getLabelsForColumn($colNum + 1);
                $r = &$this->runRanges->getRangesForColumn($colNum + 1);
                foreach ($this->rules as $rule) {
                    $rule->apply($sequence, $blackRuns, $r);
                    $updateCounter += $this->processUpdateCounter($rule);
                }
            }
        } while ($updateCounter > 0);

        return $this->field;
    }

    private function processUpdateCounter(AbstractRuleJ54 $rule)
    {
        $updates = $rule->getUpdateCounter();
        $ruleClass = get_class($rule);
        $key = substr($ruleClass, strrpos($ruleClass, '\\') + 1);
        if (!isset($this->ruleActionCounter[$key])) {
            $this->ruleActionCounter[$key] = 0;
        }
        $this->ruleActionCounter[$key] += $updates;
        return $updates;
    }

    /**
     * Getter for property "ruleActionCounter"
     *
     * @return array
     */
    public function getRuleActionCounter()
    {
        return $this->ruleActionCounter;
    }
}
