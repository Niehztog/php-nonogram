<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class RuleJ54_1_2 extends AbstractRuleJ54
{

    /**
     * RuleJ54_1_2 constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     */
    public function __construct(\Nonogram\Cell\Factory $cellFactory)
    {
        parent::__construct($cellFactory);
    }

    /**
     * When a cell does not belong to the run range of any black
     * run, the cell should be left empty.
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        $k = count($blackRuns);

        foreach ($row as $i => $cell) {
            if (empty($r) || $i < $r[1]['s'] || $i > $r[$k]['e']) {
                $this->determineCell($i, \Nonogram\Cell\AnyCell::TYPE_EMPTY);
            }
            for ($j=1;$j<$k;$j++) {
                if ($r[$j]['e'] < $i && $i < $r[$j+1]['s']) {
                    $this->determineCell($i, AnyCell::TYPE_EMPTY);
                }
            }
        }

        if(empty($r)) {
            return self::RESULT_LINE_SOLVED;
        }
    }
}
