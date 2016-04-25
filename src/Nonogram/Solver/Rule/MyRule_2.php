<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class MyRule_2 extends AbstractRuleJ54
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * scale down the range of each black run j when it contains one black segment
     * which does not belong to any other run
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($j=1;$j<=count($blackRuns);$j++) {
            $LB = $blackRuns[$j-1];

            $B = $this->findSegmentsInRange($row,$r[$j],array(AnyCell::TYPE_BOX));
            if(count($B) !== 1) {
                continue;
            }
            $coveringRuns = $this->findCoveringBlackRuns(range($B[1]['s'], $B[1]['e']), $r);
            if(count($coveringRuns) > 1 || $coveringRuns[0] !== $j) {
                continue;
            }
            
            if($B[1]['e']-$LB+1>$r[$j]['s']) {
                $r[$j]['s'] = $this->refineRange($j,'s',$B[1]['e']-$LB+1);
            }

            if($B[1]['s']+$LB-1<$r[$j]['e']) {
                $r[$j]['e'] = $this->refineRange($j,'e',$B[1]['s']+$LB-1);
            }
            
        }
    }
}
