<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class MyRule_3 extends AbstractRuleJ54
{

    /**
     * MyRule_3 constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     */
    public function __construct(\Nonogram\Cell\Factory $cellFactory)
    {
        parent::__construct($cellFactory);
    }

    /**
     * scale down the range of black runs with length 1 when rjs+1 or rje-1 is colored
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($j=1;$j<=count($blackRuns);$j++) {
            $LB = $blackRuns[$j-1];
            if($LB > 1) {
                continue;
            }

            if(isset($row[$r[$j]['s'] + 1]) && $row[$r[$j]['s'] + 1]->getType() == AnyCell::TYPE_BOX) {
                $this->refineRange($j,'s',$r[$j]['s'] + 1);
            }
            if(isset($row[$r[$j]['e'] - 1]) && $row[$r[$j]['e'] - 1]->getType() == AnyCell::TYPE_BOX) {
                $this->refineRange($j,'e',$r[$j]['e'] - 1);
            }
        }
    }
}
