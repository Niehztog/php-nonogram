<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class RuleJ54_2_2 extends AbstractRuleJ54
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * There should be at least one empty cell between two consecutive
     * black runs, so we should update the range of black
     * run j if cell crjs−1 or crje+1 is colored.
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($j=1;$j<=count($blackRuns);$j++) {
            if(isset($row[$r[$j]['s']-1]) && $row[$r[$j]['s']-1]->getType() === AnyCell::TYPE_BOX) {
                $this->refineRange($j, 's', $r[$j]['s']+1);
            }
            if(isset($row[$r[$j]['e']+1]) && $row[$r[$j]['e']+1]->getType() === AnyCell::TYPE_BOX) {
                $this->refineRange($j, 'e', $r[$j]['e']-1);
            }
        }
    }
}
