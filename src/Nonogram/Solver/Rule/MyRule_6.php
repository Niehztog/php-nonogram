<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class MyRule_6 extends AbstractRuleJ54
{

    /**
     * MyRule_6 constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     */
    public function __construct(\Nonogram\Cell\Factory $cellFactory)
    {
        parent::__construct($cellFactory);
    }

    /**
     * Look for completely unveiled black runs and limit their ranges.
     * Furthermore no other black runs range can lie in the range of
     * a finished run.
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        if(empty($blackRuns)) {
            return;
        }

        $runLengths = array();
        foreach($blackRuns as $run) {
            //count and group black run lengths
            if(!isset($runLengths[$run])) {
                $runLengths[$run] = 1;
                continue;
            }
            $runLengths[$run]++;
        }

        //find delimited segments
        $delimitedSegments = array();
        $newSegmentStart = null;
        $newSegmentEnd = null;
        $segmentLengths = array();

        for($i=0;$i<count($row);$i++) {
            if(AnyCell::TYPE_EMPTY === $row[$i]->getType()) {
                if(null === $newSegmentEnd) {
                    $newSegmentStart = $i + 1;
                }
                else {
                    //new segment found
                    $segmentLength = $newSegmentEnd - $newSegmentStart + 1;
                    $delimitedSegments[] = array('s' => $newSegmentStart, 'e' => $newSegmentEnd, 'length' => $segmentLength);
                    $newSegmentStart = null;
                    $newSegmentEnd = null;
                    if(!isset($segmentLengths[$segmentLength])) {
                        $segmentLengths[$segmentLength] = 1;
                    }
                    else {
                        $segmentLengths[$segmentLength]++;
                    }
                }
            }
            elseif(AnyCell::TYPE_UNKNOWN === $row[$i]->getType()) {
                //no finished segment
                $newSegmentStart = null;
                $newSegmentEnd = null;
            }
            elseif($newSegmentStart !== null) {
                //found box, increase segment end
                $newSegmentEnd = $i;
            }
        }

        //two cases:
        // 1) exactly one run range includes the segment --> update range to fit segment exactly
        // 2) the numbers of segments with same length equals the number of black runs with the corresponding length  --> update ranges to fit segments exactly
        $alreadyProcessed = array();
        foreach($delimitedSegments as $segment) {
            if(!isset($runLengths[$segment['length']])) {
                throw new \RuntimeException('found a black segment which is not covered by a black run');
            }
            if($runLengths[$segment['length']] === $segmentLengths[$segment['length']]) {
                //find index of first black run with the length of the segment
                foreach($blackRuns as $j => $run) {
                    if($run == $segment['length'] && !isset($alreadyProcessed[$j])) {
                        $this->refineRange($j+1, 's', $segment['s']);
                        $this->refineRange($j+1, 'e', $segment['e']);
                        $alreadyProcessed[$j] = true;
                        break;
                    }
                }
            }
            else {
                //check if the range of no other run's range with the same length includes the segment
                $coveringRuns = $this->findCoveringBlackRuns($segment['s'], $segment['e'], $r, $segment['length']);
                if(count($coveringRuns) === 1) {
                    $this->refineRange($coveringRuns[0], 's', $segment['s']);
                    $this->refineRange($coveringRuns[0], 'e', $segment['e']);
                }
            }
        }
    }
}
