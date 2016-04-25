<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class MyRule_4 extends AbstractRuleJ54
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the first(direction=right) or last(direction=left) colored cell (rjs/rje)
     *
     * @param $j
     * @param string $direction left, right
     * @param array $row
     * @param array $blackRuns
     * @param array $r
     * @return mixed
     */
    private function getOuterPositionOfRun($j, $direction, array $row, array $blackRuns, array $r)
    {
        $LB = $blackRuns[$j-1];
        $remainingLength = $LB;
        if('right' === $direction) {
            $loopStart = $r[$j]['e'];
            $loopEnd = $r[$j]['s'];
            $loopIncrement = -1;
            $loopCondition = function($i,$border) {return $i>=$border;};
        }
        else {
            $loopStart = $r[$j]['s'];
            $loopEnd = $r[$j]['e'];
            $loopIncrement = 1;
            $loopCondition = function($i,$border) {return $i<=$border;};
        }
        
        for($i=$loopStart;$loopCondition($i,$loopEnd);$i=$i+$loopIncrement) {
            if($row[$i]->getType() == AnyCell::TYPE_EMPTY) {
                $remainingLength = $LB; //start all over
            }
            else {
                $remainingLength--;
                if($remainingLength === 0) {
                    break;
                }
            }
        }
        if($remainingLength > 0) {
            throw new \RuntimeException(sprintf('black run #%d does not fit within its range (%d/%d)', $j, $r[$j]['s'], $r[$j]['e']));
        }
        return $i;
    }

    /**
     * Returns the first colored cell (rjs)
     */
    private function getRightmostPositionOfRun($j, array $row, array $blackRuns, array $r)
    {
        return $this->getOuterPositionOfRun($j, 'right', $row, $blackRuns, $r);
    }

    /**
     * Returns the last colored cell (rjs)
     */
    private function getLeftmostPositionOfRun($j, array $row, array $blackRuns, array $r)
    {
        return $this->getOuterPositionOfRun($j, 'left', $row, $blackRuns, $r);
    }

    /**
     * Some unveiled empty cells may limit the run ranges of black runs in one row.
     * This rule is for updating the run ranges to this new scenario.
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        $k = count($blackRuns);

        //foreach black run j (right to left)
        for ($j=$k-1;$j>=1;$j--) {

            //get rightmost position of next run
            $jAfterStart = $this->getRightmostPositionOfRun($j+1, $row, $blackRuns, $r);

            //if the leftmost/rightmost position of the run closest to j <= rje+( >=rjs-1),
            //shift rje/rjs +/- 1
            if($r[$j]['e']>=$jAfterStart-1) {
                $newEnd = $this->findFirstCellBefore($row,$jAfterStart-1,array(AnyCell::TYPE_BOX, AnyCell::TYPE_UNKNOWN), $r[$j]['s']);
                if(false === $newEnd) {
                    throw new \RuntimeException('failed to find new range end for run '.$j);
                }
                $r[$j]['e'] = $this->refineRange($j,'e',$newEnd);
            }

        }

        //foreach black run j (left to right)
        for ($j=2;$j<=$k;$j++) {

            //get leftmost position of previous run
            $jBeforeEnd = $this->getLeftmostPositionOfRun($j-1, $row, $blackRuns, $r);

            //if the leftmost/rightmost position of the run closest to j <= rje+( >=rjs-1),
            //shift rje/rjs +/- 1
            if($r[$j]['s']<=$jBeforeEnd+1) {
                $newStart = $this->findFirstCellAfter($row,$jBeforeEnd+1,array(AnyCell::TYPE_BOX, AnyCell::TYPE_UNKNOWN), $r[$j]['e']);
                if(false === $newStart) {
                    throw new \RuntimeException('failed to find new range start for run '.$j);
                }
                $r[$j]['s'] = $this->refineRange($j,'s',$newStart);
            }

        }
    }
}
