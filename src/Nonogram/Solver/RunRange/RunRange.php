<?php

namespace Nonogram\Solver\RunRange;

/**
 * Class RunRange
 * @package Nonogram\Solver\RunRange
 */
class RunRange
{

    private \Nonogram\Label\Label $labels;
    
    /**
     * Numeric column labels = tuple of numbers
     */
    private array $col = [];

    /**
     * Numeric row labels = tuple of numbers
     */
    private array $row = [];

    /**
     * @param \Nonogram\Label\Label $labels
     */
    public function setLabels(\Nonogram\Label\Label $labels)
    {
        $this->labels = $labels;
        $this->initialRunRangeEstimation();
    }

    private function initialRunRangeEstimation()
    {
        //rows
        for($ir=1;$ir<=$this->labels->getSizeY();$ir++) {
            $this->row[$ir-1] = $this->initRunRangeFor($ir);
        }

        //columns
        for($ic=1;$ic<=$this->labels->getSizeX();$ic++) {
            $this->col[$ic-1] = $this->initRunRangeFor(null, $ic);
        }
    }

    /**
     * Numbering of black runs: j
     * Count of black runs in a row: k
     * Row size (amount of cells): n
     * left most starting position of run j: rjs
     * right most ending position of run j: rje
     * length of black run: LB
     *
     * @param null $rowNumber
     * @param null $colNumber
     * @return array
     */
    private function initRunRangeFor($rowNumber = null, $colNumber = null)
    {
        if (!($rowNumber == null xor $colNumber == null)) {
            throw new \RuntimeException('specify either row OR col number and not both');
        }

        $r = [];
        $labels = $colNumber != null ? $this->labels->getLabelsForColumn($colNumber) : $this->labels->getLabelsForRow($rowNumber);
        if(empty($labels)) {
            return $r;
        }
        $n = $colNumber != null ? $this->labels->getSizeY() : $this->labels->getSizeX();
        $k = count($labels);

        $r[1]['s'] = 0;
        for ($j=2; $j<=$k; $j++) {
            $r[$j]['s'] = 0;
            for ($i = 1;$i<=$j-1;$i++) {
                $LB = $labels[$i-1];
                $r[$j]['s'] += $LB + 1;
            }
        }

        for ($j=1; $j<=$k-1; $j++) {
            $r[$j]['e'] = $n - 1;
            for ($i = $j + 1;$i<=$k;$i++) {
                $LB = $labels[$i-1];
                $r[$j]['e'] -= $LB + 1;
            }
        }
        $r[$k]['e'] = $n - 1;

        return $r;
    }

    public function &getRangesForColumn(int $index)
    {
        return $this->getRanges(false, $index);
    }

    /**
     *
     * @param int $index [1..n]
     */
    public function &getRangesForRow(int $index)
    {
        return $this->getRanges(true, $index);
    }

    private function &getRanges(bool $horizontal, int $index)
    {
        if ($horizontal) {
            return $this->row[$index-1];
        }
        return $this->col[$index-1];
    }
}
