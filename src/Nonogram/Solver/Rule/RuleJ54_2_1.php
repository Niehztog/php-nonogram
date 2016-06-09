<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class RuleJ54_2_1 extends AbstractRuleJ54
{

    /**
     * RuleJ54_2_1 constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     */
    public function __construct(\Nonogram\Cell\Factory $cellFactory)
    {
        parent::__construct($cellFactory);
    }

    /**
     * update the range of each black run j
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($j=1;$j<=count($blackRuns);$j++) {
            if(isset($r[$j-1]) && $r[$j]['s'] <= $r[$j-1]['s']) {
                $r[$j]['s'] = $this->refineRange($j, 's', $r[$j-1]['s'] + $blackRuns[$j-2] + 1);
            }
            if(isset($r[$j+1]) && $r[$j]['e'] >= $r[$j+1]['e']) {
                $r[$j]['e'] = $this->refineRange($j, 'e', $r[$j+1]['e'] - $blackRuns[$j] - 1);
            }
        }
    }
}
