<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class RuleJ54_3_1 extends AbstractRuleJ54
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * When several colored cells belonging to the same black run
     * are scattered, all unknown cells among them should be
     * colored to form a new black segment, and the run range can
     * also be updated Rule 3.1 is presented to treat this situation.
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($j=1;$j<=count($blackRuns);$j++) {
            $LB = $blackRuns[$j-1];

            $cm = $this->findFirstCellAfter($row, (isset($r[$j-1]) ? $r[$j-1]['e'] : -1), AnyCell::TYPE_BOX, $r[$j]['e']);
            $cn = $this->findFirstCellBefore($row, (isset($r[$j+1]) ? $r[$j+1]['s'] : count($row)), AnyCell::TYPE_BOX, $r[$j]['s']);
            if (false === $cm || false === $cn || $cm >= $cn) {
                continue;
            }
            foreach (range($cm, $cn) as $pos) {
                $this->determineCell($pos, AnyCell::TYPE_BOX);
            }

            try {
                $u = $LB - ($cn - $cm + 1);
                $r[$j]['s'] = $this->refineRange($j, 's', $cm - $u);
                $r[$j]['e'] = $this->refineRange($j, 'e', $cn + $u);
            }
            catch(\OutOfBoundsException $e) {
                continue;
            }
        }
    }
}
