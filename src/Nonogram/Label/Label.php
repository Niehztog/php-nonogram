<?php

namespace Nonogram\Label;

class Label
{
    /**
     * Numeric column labels = tuple of numbers
     *
     * @var array
     */
    private $col = [];

    /**
     * Numeric row labels = tuple of numbers
     *
     * @var array
     */
    private $row = [];

    /**
     * @var int
     */
    private $sizeX;

    /**
     * @var int
     */
    private $sizeY;

    /**
     * @var int
     */
    private $cacheMaxAmountRow;

    /**
     * @var int
     */
    private $cacheMaxAmountCol;

    /**
     * @var int
     */
    private $cacheMaxDigitCount;

    /**
     * @param array $labels
     */
    public function setCol(array $labels)
    {
        $this->col = $labels;
        $this->sizeX = count($labels);
    }

    /**
     * @param array $labels
     */
    public function setRow(array $labels)
    {
        $this->row = $labels;
        $this->sizeY = count($labels);
    }

    /**
     * @return array
     */
    public function getCol()
    {
        return $this->col;
    }

    /**
     * @return array
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * required for drawing the field and for autosolving
     *
     * @param $index
     */
    public function getLabelsForColumn($index)
    {
        return $this->getLabels(false, $index);
    }

    /**
     * required for drawing the field and for autosolving
     *
     * @param int $index [1..n]
     */
    public function getLabelsForRow($index)
    {
        return $this->getLabels(true, $index);
    }

    /**
     * @param $horizontal
     * @param $index
     */
    private function getLabels($horizontal, $index)
    {
        $seq = $horizontal ? $this->getRow() : $this->getCol();
        return $seq[$index-1];
    }

    /**
     * required for drawing the field, how much space to leave blank for numbers
     * @return int
     */
    public function getMaxAmountVertical()
    {
        return isset($this->cacheMaxAmountCol) ? $this->cacheMaxAmountCol : $this->cacheMaxAmountCol = $this->getMaxAmount(false);
    }

    /**
     * required for drawing the field, how much space to leave blank for numbers
     * @return int
     */
    public function getMaxAmountHorizontal()
    {
        return isset($this->cacheMaxAmountRow) ? $this->cacheMaxAmountRow : $this->cacheMaxAmountRow = $this->getMaxAmount(true);
    }

    /**
     * For all labels describing horizontal rows, this method searches for
     * the highest number and returns the amount of digits necessary to
     * represent it. For example
     * 7- 1 digit
     * 11 - 2 digits
     * @return int
     */
    public function getMaxDigitCount()
    {
        if(isset($this->cacheMaxDigitCount)) {
            return $this->cacheMaxDigitCount;
        }

        $totalMax = 0;
        $rowLabels = $this->getRow();
        foreach ($rowLabels as $labelsRow) {
            if(empty($labelsRow)) {
                continue;
            }
            $max = max($labelsRow);
            $maxLength = strlen((string)$max);
            if ($maxLength > $totalMax) {
                $totalMax = $maxLength;
            }
        }

        return $this->cacheMaxDigitCount = $totalMax;
    }

    /**
     * Tells whether any of the numbers in the labels euqals to 0,
     * which means that the actual run-length is kept secret
     *
     * @return bool
     */
    public function hasHiddenCounts()
    {
        foreach(array('getCol', 'getRow') as $getter) {
            $data = $this->$getter();
            foreach ($data as $i => $seq) {
                foreach ($seq as $j => $count) {
                    if($count === '0') {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * required for drawing the field, how much space to leave blank for numbers
     * @return int
     */
    private function getMaxAmount($horizontal)
    {
        $labelVariable = $horizontal ? 'row' : 'col';
        $max = 0;
        foreach ($this->$labelVariable as $labels) {
            $count = count($labels);
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    /**
     * @return mixed
     */
    public function getSizeX()
    {
        return $this->sizeX;
    }

    /**
     * @return mixed
     */
    public function getSizeY()
    {
        return $this->sizeY;
    }
}
