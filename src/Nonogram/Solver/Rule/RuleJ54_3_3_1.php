<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class RuleJ54_3_3_1 extends AbstractRuleJ54
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * This rule is designed for solving the situations that the range
     * of black run j do not overlap the range of black run j − 1
     * or j + 1.
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($j=1;$j<=count($blackRuns);$j++) {
            $LB = $blackRuns[$j-1];

            if ($row[$r[$j]['s']]->getType() === AnyCell::TYPE_BOX && (!isset($r[$j-1]) || $r[$j-1]['e'] < $r[$j]['s'])) {
                if($LB > 1) {
                    foreach (range($r[$j]['s'] + 1, $r[$j]['s'] + $LB - 1) as $ci) {
                        $this->determineCell($ci, AnyCell::TYPE_BOX);
                    }
                }
                if ($r[$j]['s'] - 1 >= 0) {
                    $this->determineCell($r[$j]['s'] - 1, AnyCell::TYPE_EMPTY);
                }
                if ($r[$j]['s'] + $LB < count($row)) {
                    $this->determineCell($r[$j]['s'] + $LB, AnyCell::TYPE_EMPTY);
                }

                $r[$j]['e'] = $this->refineRange($j, 'e', $r[$j]['s'] + $LB - 1);
                if (isset($r[$j + 1]) && $r[$j + 1]['s'] <= $r[$j]['e']) {
                    $r[$j + 1]['s'] = $this->refineRange($j + 1, 's', $r[$j]['e'] + 2);
                }
                if (isset($r[$j - 1]) && $r[$j - 1]['e'] == $r[$j]['s'] - 1) {
                    $r[$j - 1]['e'] = $this->refineRange($j - 1, 'e', $r[$j]['s'] - 2);
                }
            }

            if ($row[$r[$j]['e']]->getType() === AnyCell::TYPE_BOX && (!isset($r[$j+1]) || $r[$j+1]['s'] > $r[$j]['e'])) {
                if($LB > 1) {
                    foreach (range($r[$j]['e'] - $LB + 1, $r[$j]['e'] - 1) as $ci) {
                        $this->determineCell($ci, AnyCell::TYPE_BOX);
                    }
                }
                if ($r[$j]['e'] + 1 < count($row)) {
                    $this->determineCell($r[$j]['e'] + 1, AnyCell::TYPE_EMPTY);
                }
                if ($r[$j]['e'] - $LB >= 0) {
                    $this->determineCell($r[$j]['e'] - $LB, AnyCell::TYPE_EMPTY);
                }

                $r[$j]['s'] = $this->refineRange($j, 's', $r[$j]['e'] - $LB + 1);
                if (isset($r[$j - 1]) && $r[$j - 1]['e'] >= $r[$j]['s']) {
                    $r[$j - 1]['e'] = $this->refineRange($j - 1, 'e', $r[$j]['s'] - 2);
                }
                if (isset($r[$j + 1]) && $r[$j + 1]['s'] == $r[$j]['e'] + 1) {
                    $r[$j + 1]['s'] = $this->refineRange($j + 1, 's', $r[$j]['e'] + 2);
                }
            }

        }
    }
}
