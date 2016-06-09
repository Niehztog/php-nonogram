<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class MyRule_1 extends AbstractRuleJ54
{

    /**
     * MyRule_1 constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     */
    public function __construct(\Nonogram\Cell\Factory $cellFactory)
    {
        parent::__construct($cellFactory);
    }

    /**
     * scale down the range of each black run j when the edges of the range are covered with empty cells
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($j=1;$j<=count($blackRuns);$j++) {
            $LB = $blackRuns[$j-1];

            for($s=$r[$j]['s'];$s<=$r[$j]['e']-$LB;$s++) {
                if($row[$s]->getType() === AnyCell::TYPE_EMPTY) {
                    $this->refineRange($j,'s',$r[$j]['s']+1);
                }
                else {
                    break;
                }
            }

            for($e=$r[$j]['e'];$e>=$r[$j]['s']+$LB;$e--) {
                if($row[$e]->getType() === AnyCell::TYPE_EMPTY) {
                    $this->refineRange($j,'e',$r[$j]['e']-1);
                }
                else {
                    break;
                }
            }

        }
    }
}
