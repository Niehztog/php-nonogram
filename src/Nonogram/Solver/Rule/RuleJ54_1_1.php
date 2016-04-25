<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class RuleJ54_1_1 extends AbstractRuleJ54
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * For each black run, those cells in the intersection
     * of the left-most possible solution of the black run and the
     * right-most possible solution of the black run must be colored.
     *
     * The intersection exists when the length of the black run’s
     * range is less than two times the actual length of the black
     * run.
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($j=1;$j<=count($blackRuns);$j++) {
            $LB = $blackRuns[$j-1];
            foreach ($row as $i => $cell) {
                $u = $r[$j]['e'] - $r[$j]['s'] + 1 - $LB;
                if ($r[$j]['s']+$u<=$i && $i <= $r[$j]['e'] - $u) {
                    $this->determineCell($i, AnyCell::TYPE_BOX);
                }
            }
        }
    }
}
