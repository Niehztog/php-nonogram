<?php

namespace Nonogram\Solver\Rule;

use Nonogram\Cell\AnyCell;

abstract class AbstractRuleJ54
{
    /**
     * @var bool
     */
    const RESULT_LINE_SOLVED = true;
    
    /**
     * @var array
     */
    private $row;

    /**
     * @var array
     */
    private $runRanges;

    /**
     * @var \Nonogram\Cell\Factory
     */
    private $cellFactory;

    /**
     * @var int
     */
    private $updateCounter;

    /**
     * AbstractRuleJ54 constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     */
    public function __construct(\Nonogram\Cell\Factory $cellFactory)
    {
        $this->cellFactory = $cellFactory;
        $this->cellFactory->setStatusHidden(false);
    }

    /**
     * Getter for property "updateCounter"
     * @return int
     */
    public function getUpdateCounter()
    {
        return $this->updateCounter;
    }

    /**
     * Returns an array of black run numbers, whose range
     * contains *all* given cell(s)
     *
     * @param $cellIndexStart
     * @param $cellIndexEnd
     * @param array $r
     * @param null $lengthFilter if set, only runs with the specified length will be included in the result
     * @return array [0..k]
     */
    protected function findCoveringBlackRuns($cellIndexStart, $cellIndexEnd, array $r, $lengthFilter = null)
    {
        if ($cellIndexStart > $cellIndexEnd) {
            throw new \InvalidArgumentException(sprintf('invalid arguments for cellIndexStart/End (%d,%d)', $cellIndexStart, $cellIndexEnd));
        }

        $intersectingBlackRuns = [];

        foreach ($r as $j => $range) {
            if ($range['s'] <= $cellIndexStart && $cellIndexEnd <= $range['e'] && (null === $lengthFilter || $lengthFilter === $range['e'] - $range['s'] + 1)) {
                $intersectingBlackRuns[] = $j;
            }
        }

        return $intersectingBlackRuns;
    }

    /**
     * @param $row
     * @param $cellNumber
     * @return mixed
     */
    protected function getSegmentStart($row, $cellNumber)
    {
        for ($i=$cellNumber-1;$i>=0;$i--) {
            if ($row[$i]->getType() !== $row[$i]::TYPE_BOX) {
                return $i+1;
            }
        }
        return $i+1;
    }

    /**
     * @param $row
     * @param $cellNumber
     * @return mixed
     */
    protected function getSegmentEnd($row, $cellNumber)
    {
        for ($i=$cellNumber+1;$i<count($row);$i++) {
            if ($row[$i]->getType() !== $row[$i]::TYPE_BOX) {
                return $i-1;
            }
        }
        return $i-1;
    }

    /**
     * calculates the length of a black segment (consecutive black blocks) specified
     * by any cell coordinate inside the segment
     *
     * @param AnyCell[] $row
     * @param $cellNumber
     * @return int
     */
    protected function getSegmentLength($row, $cellNumber)
    {
        $segmentStart = $this->getSegmentStart($row, $cellNumber);
        $segmentEnd = $this->getSegmentEnd($row, $cellNumber);

        return $segmentEnd-$segmentStart+1;
    }

    /**
     * @param $rj
     * @return mixed
     */
    protected function getRangeLength($rj)
    {
        return $rj['e']-$rj['s']+1;
    }

    /**
     * TODO: Maybe possible optimization: also count (outer) black cells next to segments within
     * the range as belonging to the segment. Segments could more often be larger then,
     * allowing the rule to take effect more often
     * If a segment lies only partly inside the range of the black run, it can't belong to the run
     *
     * @param AnyCell[] $row
     * @param array $rj
     * @param array|int $allowedCellTypes
     * @param int $maxLength (optional)
     * @return array [1..k]
     */
    protected function findSegmentsInRange(array $row, array $rj, $allowedCellTypes, $maxLength = null)
    {
        $allowedCellTypes = (array)$allowedCellTypes;

        $segments = [];
        if(0 === $maxLength) {
            return $segments;
        }
                
        $segKey = 1;
        for ($i=$rj['s'];$i<=$rj['e'];$i++) {
            if (!in_array($row[$i]->getType(), $allowedCellTypes)) {
                if (isset($segments[$segKey]['s'])) {
                    $segments[$segKey]['e'] = $i-1;
                    if($maxLength > 0 && $this->getRangeLength($segments[$segKey])>$maxLength) {
                        unset($segments[$segKey]);
                    }
                    else {
                        $segKey++;
                    }
                }
                continue;
            }
            if (empty($segments[$segKey])) {
                $segments[$segKey]['s'] = $i;
            }
        }
        //close last open segment if necessary
        if (isset($segments[$segKey]['s']) && !isset($segments[$segKey]['e'])) {
            $segments[$segKey]['e'] = $i-1;
            if($maxLength > 0 && $this->getRangeLength($segments[$segKey])>$maxLength) {
                unset($segments[$segKey]);
            }
        }
        return $segments;
    }

    /**
     * @param array $row
     * @param $pos
     * @param $allowedCellTypes
     * @param null $max
     * @return bool
     */
    protected function findFirstCellAfter(array $row, $pos, $allowedCellTypes, $max = null)
    {
        $allowedCellTypes = (array)$allowedCellTypes;

        if (null === $max) {
            $max = count($row) - 1;
        }
        for ($i=$pos+1;$i<=$max;$i++) {
            if (in_array($row[$i]->getType(), $allowedCellTypes)) {
                return $i;
            }
        }
        return false;
    }

    /**
     * @param array $row
     * @param $pos
     * @param $allowedCellTypes
     * @param null $min
     * @return bool
     */
    protected function findFirstCellBefore(array $row, $pos, $allowedCellTypes, $min = null)
    {
        $allowedCellTypes = (array)$allowedCellTypes;

        if (null === $min) {
            $min = 0;
        }
        for ($i=$pos-1;$i>=$min;$i--) {
            if (in_array($row[$i]->getType(), $allowedCellTypes)) {
                return $i;
            }
        }
        return false;
    }

    /**
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     */
    public function apply(array &$row, array $blackRuns, array &$r)
    {
        $this->updateCounter = 0;
        $this->row = &$row;
        $this->runRanges = &$r;
        return $this->_apply($row, $blackRuns, $r);
    }

    /**
     * @param AnyCell[] $row
     * @param array $blackRuns
     * @param array $r
     * @return bool|null returns true only if the row is completely solved
     */
    abstract protected function _apply(array $row, array $blackRuns, array $r);

    /**
     * @param $i
     * @param $type
     */
    protected function determineCell($i, $type)
    {
        if(!array_key_exists($i,$this->row)) {
            throw new \OutOfBoundsException(sprintf('invalid cell identifier %d', $i));
        }

        if ($this->row[$i]->getType() !== $type && $type !== AnyCell::TYPE_UNKNOWN) {
            if($this->row[$i]->getType() !== AnyCell::TYPE_UNKNOWN) {
                throw new \RuntimeException('puzzle has no solution');
            }
            $this->row[$i] = $this->cellFactory->getByType($type);
            $this->updateCounter++;
        }
    }

    /**
     * Refine the range of a black run
     * @param $j
     * @param $startEnd
     * @param $value
     * @return int
     */
    protected function refineRange($j, $startEnd, $value)
    {
        if(!array_key_exists($j,$this->runRanges)) {
            throw new \OutOfBoundsException(sprintf('invalid range identifier %d', $j));
        }

        if ($this->runRanges[$j][$startEnd] !== $value) {
            if(!array_key_exists($value,$this->row)) {
                throw new \OutOfBoundsException(sprintf('invalid cell identifier %d', $value));
            }

            $this->runRanges[$j][$startEnd] = $value;
            $this->updateCounter++;
        }
        return $value;
    }
}
