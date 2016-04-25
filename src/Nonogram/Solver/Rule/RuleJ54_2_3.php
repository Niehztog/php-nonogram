<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class RuleJ54_2_3 extends AbstractRuleJ54
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * In the range of a black run j , maybe one or more than one
     * black segment exist. Some black segments may have lengths
     * larger than LBj , but some not. For each black segment with
     * length larger than LBj , if we can determine that it belongs
     * to the former black runs of run j or the later ones, we can
     * update the range of black run j.
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($j=1;$j<=count($blackRuns);$j++) {
            $LB = $blackRuns[$j-1];
            //find out all black segments in (rjs, rje)
            $B = $this->findSegmentsInRange($row, $r[$j], array(AnyCell::TYPE_BOX));
            foreach ($B as $i) {
                if ($this->getRangeLength($i) > $LB) {

                    $coveringBlackRuns = $this->findCoveringBlackRuns(range($i['s'],$i['e']), $r);
                    if(($key = array_search($j, $coveringBlackRuns)) !== false) {
                        unset($coveringBlackRuns[$key]);
                    }

                    if(empty($coveringBlackRuns)) {
                        continue;
                    }

                    if(max($coveringBlackRuns)<$j) {
                        $r[$j]['s'] = $this->refineRange($j, 's', $i['e']+2);
                    }
                    if(min($coveringBlackRuns)>$j) {
                        $r[$j]['e'] = $this->refineRange($j, 'e', $i['s']-2);
                    }
                }
            }
        }
    }
}
