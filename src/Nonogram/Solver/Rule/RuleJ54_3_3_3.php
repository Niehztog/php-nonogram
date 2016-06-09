<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class RuleJ54_3_3_3 extends AbstractRuleJ54
{

    /**
     * RuleJ54_3_3_3 constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     */
    public function __construct(\Nonogram\Cell\Factory $cellFactory)
    {
        parent::__construct($cellFactory);
    }

    /**
     * There is more than one black segment in the range of
     * black run j. In (rjs, rje), find the first and second
     * black segments. If the length of the new run after
     * merging these two black segments by coloring those cells
     * between these two segments is larger than LBj, then
     * these two segments should not belong to the same run.
     * Otherwise, keep checking the length of the new run
     * after merging the first and third black segments. The
     * process will be repeated until all black segments in
     * (rjs,rje) have been checked or a black segment i is
     * found such that the length of the new run after merging
     * the first black segment and black segment i is larger
     * than LBj. Rule 3.3-3 is provided to deal with this
     * situation. Figure 17 gives an example.
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        for ($j=1;$j<=count($blackRuns);$j++) {
            $LB = $blackRuns[$j-1];

            if(!isset($r[$j-1])||$r[$j-1]['e']<$r[$j]['s']) {
                $B = $this->findSegmentsInRange($row,$r[$j],AnyCell::TYPE_BOX);
                $b = count($B);
                if($b > 1) {
                    for ($i=1;$i<=$b;$i++) { //TODO: condition "<" might be sufficient
                        $cs = $B[$i]['s'];

                        for ($m=$i+1;$m<=$b;$m++) {
                            $ct = $B[$m]['s'];
                            $ce = $B[$m]['e'];
                            if (($ce - $cs + 1) > $LB) {
                                $r[$j]['e'] = $this->refineRange($j, 'e', $ct - 2);
                                break;
                            }
                            continue;
                        }

                    }
                }
            }

            if(!isset($r[$j+1])||$r[$j+1]['s']>$r[$j]['e']) {
                $B = $this->findSegmentsInRange($row,$r[$j],AnyCell::TYPE_BOX);
                $b = count($B);
                if($b > 1) {
                    for ($i=$b;$i>0;$i--) {
                        $ce = $B[$i]['e'];

                        for ($m=$i-1;$m>0;$m--) {
                            $cs = $B[$m]['s'];
                            $ct = $B[$m]['e'];

                            if (($ce - $cs + 1) > $LB) {
                                $r[$j]['s'] = $this->refineRange($j, 's', $ct + 2);
                                break;
                            }
                            continue;
                        }

                    }
                }
            }

        }
    }
}
