<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class RuleJ54_3_3_2 extends AbstractRuleJ54
{

    /**
     * RuleJ54_3_3_2 constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     */
    public function __construct(\Nonogram\Cell\Factory $cellFactory)
    {
        parent::__construct($cellFactory);
    }

    /**
     * An empty cell cw appears after a black cell cb
     * It should be true that each cell after cw will not belong
     * to black run j.
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($j=1;$j<=count($blackRuns);$j++) {
            if (!isset($r[$j-1]) || $r[$j-1]['e'] < $r[$j]['s']) {
                $cb = $this->findFirstCellAfter($row, $r[$j]['s']-1, AnyCell::TYPE_BOX, $r[$j]['e']);
                if (false !== $cb && $cb<$r[$j]['e']) {
                    $cw = $this->findFirstCellAfter($row, $cb, AnyCell::TYPE_EMPTY, $r[$j]['e']);
                    if (false !== $cw) {
                        $r[$j]['e'] = $this->refineRange($j, 'e', $cw-1);
                    }
                }
            }

            if (!isset($r[$j+1]) || $r[$j+1]['s'] > $r[$j]['e']) {
                $cb = $this->findFirstCellBefore($row, $r[$j]['e']+1, AnyCell::TYPE_BOX, $r[$j]['s']);
                if (false !== $cb && $cb>$r[$j]['s']) {
                    $cw = $this->findFirstCellBefore($row, $cb, AnyCell::TYPE_EMPTY, $r[$j]['s']);
                    if (false !== $cw) {
                        $r[$j]['s'] = $this->refineRange($j, 's', $cw+1);
                    }
                }
            }
        }
    }
}
