<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class RuleJ54_1_3 extends AbstractRuleJ54
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * For each black run j , when the first cell crjs of its run range
     * is colored and covered by other black runs, if the lengths of
     * those covering black runs are all one, cell crjs−1 should be
     * left empty (same for crje+1).
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($j=1;$j<=count($blackRuns);$j++) {
            foreach (array('s', 'e') as $startEnd) {
                //is the first cell of the runs range already colored?
                if ($row[$r[$j][$startEnd]]->getType() !== AnyCell::TYPE_BOX) {
                    continue;
                }

                //get all other black runs covering crjs
                $intersectingBlackRuns = $this->findCoveringBlackRuns(array($r[$j][$startEnd]), $r);

                //delete the black run $j from the results
                if (false !== ($key = array_search($j, $intersectingBlackRuns))) {
                    unset($intersectingBlackRuns[$key]);
                }

                if (!empty($intersectingBlackRuns)) {
                    //if the length of all covering blackruns equals 1
                    $lengthEquals1 = true;
                    foreach ($intersectingBlackRuns as $i) {
                        $LB = $blackRuns[$i - 1];
                        if ($LB > 1) {
                            $lengthEquals1 = false;
                            break;
                        }
                    }
                    if ($lengthEquals1) {
                        $this->determineCell($r[$j][$startEnd] + ('s' === $startEnd ? -1 : 1), AnyCell::TYPE_EMPTY);
                    }
                }
            }
        }
    }
}
