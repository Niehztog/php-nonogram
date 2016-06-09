<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

class MyRule_5 extends AbstractRuleJ54
{

    /**
     * MyRule_5 constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     */
    public function __construct(\Nonogram\Cell\Factory $cellFactory)
    {
        parent::__construct($cellFactory);
    }

    /**
     * Mark all unknown cells as empty if all black runs are completely unveiled
     *
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    protected function _apply(array $row, array $blackRuns, array $r)
    {
        //count black cells
        $rowSize = count($row);
        $blackCells = array_sum($blackRuns);
        $emptyCells = $rowSize - $blackCells;
        $actualUnknown = 0;
        $actualEmpty = 0;

        for($i=0;$i<$rowSize;$i++) {
            if(AnyCell::TYPE_UNKNOWN === $row[$i]->getType()) {
                $actualUnknown++;
            }elseif(AnyCell::TYPE_EMPTY === $row[$i]->getType()) {
                $actualEmpty++;
            }
            if($actualUnknown+$actualEmpty > $emptyCells) {
                return;
            }
        }

        //line finished
        if($actualUnknown === 0) {
            return self::RESULT_LINE_SOLVED;
        }

        //all black runs are discovered, mark all remaining unknown cells as empty
        for($i=0;$i<$rowSize;$i++) {
            if(AnyCell::TYPE_UNKNOWN === $row[$i]->getType()) {
                $this->determineCell($i, AnyCell::TYPE_EMPTY);
            }
        }
        return self::RESULT_LINE_SOLVED;
    }
}
