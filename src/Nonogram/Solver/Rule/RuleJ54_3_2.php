<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class RuleJ54_3_2 extends AbstractRuleJ54
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Some empty cells may be scattered over the range of black
     * run j, so there will be several segments bounded by these
     * empty cells. The lengths of some segments may be less than
     * LBj, these segments can be skipped and the run range can
     * be updated. In case these segments are not part of any other
     * runs range, they must be empty.
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($j=1;$j<=count($blackRuns);$j++) {
            $LB = $blackRuns[$j-1];

            //find out all segments bounded by empty cells in (rjs, rje)
            $B = $this->findSegmentsInRange($row, $r[$j], array(AnyCell::TYPE_BOX, AnyCell::TYPE_UNKNOWN));
            if (empty($B)) {
                continue;
            }
            $b = count($B);

            for ($i=1;$i<=$b;$i++) {
                if ($this->getRangeLength($B[$i]) < $LB) {
                    continue;
                }
                $r[$j]['s'] = $this->refineRange($j, 's', $B[$i]['s']);
                break;
            }

            for($i=$b;$i>0;$i--) {
                if ($this->getRangeLength($B[$i]) < $LB) {
                    continue;
                }
                $r[$j]['e'] = $this->refineRange($j, 'e', $B[$i]['e']);
                break;
            }

            if(1 === $LB) {
                continue;
            }
            
            $B = $this->findSegmentsInRange($row, $r[$j], array(AnyCell::TYPE_BOX, AnyCell::TYPE_UNKNOWN), $LB-1);
            $b = count($B);
            for($i=1; $i<=$b; $i++) {
                $coveringBlackRuns = $this->findCoveringBlackRuns(range($B[$i]['s'],$B[$i]['e']), $r);
                if(1 === count($coveringBlackRuns)) {
                    foreach(range($B[$i]['s'],$B[$i]['e']) as $l) {
                        $this->determineCell($l, AnyCell::TYPE_EMPTY);
                    }
                }
            }

        }
    }
}
