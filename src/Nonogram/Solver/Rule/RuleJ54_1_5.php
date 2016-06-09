<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class RuleJ54_1_5 extends AbstractRuleJ54
{

    /**
     * RuleJ54_1_5 constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     */
    public function __construct(\Nonogram\Cell\Factory $cellFactory)
    {
        parent::__construct($cellFactory);
    }

    /**
     * Calculates the minimal length of a group of black runs
     *
     * @param array $someRuns
     * @param array $allRuns
     * @param int $n Row size (amount of cells)
     * @return int|mixed
     */
    private function findShortestRun(array $someRuns, array $allRuns, $n)
    {
        $minL = $n;
        foreach ($someRuns as $run) {
            if ($allRuns[$run-1] <= $minL) {
                $minL = $allRuns[$run-1];
            }
        }
        return $minL;
    }

    /**
     * Some empty cells like walls may obstruct the expansion of
     * some black segments, we can use this property to color more
     * cells.
     * On the other hand, for a black segment covered by a series
     * of black runs, which have the same length and overlapping
     * ranges, if the length of the black segment equals to the
     * length of those black covering runs, the two cells next to the
     * two ends of the black segment are set as empty.
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($i=1;$i<=count($row)-1;$i++) {
            $cell1 = $row[$i - 1];
            $cell2 = $row[$i];

            if (!in_array($cell1->getType(), array($cell1::TYPE_EMPTY, $cell1::TYPE_UNKNOWN))
                || $cell2->getType() !== $cell2::TYPE_BOX
            ) {
                continue;
            }

            $intersectingBlackRuns = $this->findCoveringBlackRuns($i, $i, $r);
            if (empty($intersectingBlackRuns)) {
                continue;
            }
            $minL = $this->findShortestRun($intersectingBlackRuns, $blackRuns, count($row));

            //Find an empty cell cm closest to ci,m ∈ [i − minL + 1, i −1]
            $m = $this->findFirstCellBefore($row, $i, AnyCell::TYPE_EMPTY, ($i-$minL+1 >= 0 ? $i-$minL+1 : 0));
            if (false !== $m) {
                for ($p=$i+1;$p<=$m+$minL&&$p<count($row);$p++) {
                    $this->determineCell($p, AnyCell::TYPE_BOX);
                }
            }

            //Find an empty cell cn closest to ci,n ∈ [i + 1, i + minL − 1]
            $n = $this->findFirstCellAfter($row, $i, AnyCell::TYPE_EMPTY, ($i+$minL-1 <= count($row)-1 ? $i+$minL-1 : count($row)-1));
            if (false !== $n) {
                for ($p=$n-$minL;$p<=$i-1;$p++) {
                    $this->determineCell($p, AnyCell::TYPE_BOX);
                }
            }

            //get length of all black runs covering ci
            $segmentLength = $this->getSegmentLength($row, $i);
            foreach ($intersectingBlackRuns as $intersectingBlackRun) {
                if ($blackRuns[$intersectingBlackRun-1] != $segmentLength) {
                    return;
                }
            }
            $s = $this->getSegmentStart($row, $i);
            if (isset($row[$s-1])) {
                $this->determineCell($s - 1, AnyCell::TYPE_EMPTY);
            }

            $e = $this->getSegmentEnd($row, $i);
            if (isset($row[$e+1])) {
                $this->determineCell($e + 1, AnyCell::TYPE_EMPTY);
            }
        }
    }
}
