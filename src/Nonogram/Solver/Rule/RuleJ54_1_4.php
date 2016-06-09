<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class RuleJ54_1_4 extends AbstractRuleJ54
{

    /**
     * RuleJ54_1_4 constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     */
    public function __construct(\Nonogram\Cell\Factory $cellFactory)
    {
        parent::__construct($cellFactory);
    }

    /**
     * Calculates the maximal length of a group of black runs
     *
     * @param array $someRuns
     * @param array $allRuns
     * @return int|mixed
     */
    private function findLongestRun(array $someRuns, array $allRuns) {
        $maxL = 0;
        foreach ($someRuns as $run) {
            if ($allRuns[$run-1] > $maxL) {
                $maxL = $allRuns[$run-1];
            }
        }
        return $maxL;
    }

    /**
     * If two consecutive black segments with an unknown cell between
     * them are combined into a new black segment with length larger
     * than the maximal length maxL of all black runs containing part
     * of this new segment, the unknown cell should be left empty.
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        $n = count($row);

        for ($i=1;$i<=$n-2;$i++) {
            $cell1 = $row[$i-1];
            $cell2 = $row[$i];
            $cell3 = $row[$i+1];
            if ($cell1->getType() !== $cell1::TYPE_BOX
                ||  $cell2->getType() !== $cell2::TYPE_UNKNOWN
                ||  $cell3->getType() !== $cell3::TYPE_BOX
            ) {
                continue;
            }
            $intersectingBlackRuns = $this->findCoveringBlackRuns($i-1, $i+1, $r);

            $maxL = $this->findLongestRun($intersectingBlackRuns, $blackRuns);
            $segmentLength = $this->getSegmentLength($row, $i);
            if ($segmentLength > $maxL) {
                $this->determineCell($i, AnyCell::TYPE_EMPTY);
            }
        }
    }
}
