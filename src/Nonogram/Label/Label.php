<?php

namespace Nonogram\Label;

class Label
{
    /**
     * Numeric column labels = tuple of numbers
     *
     * @var array
     */
    private $col = array();

    /**
     * Numeric row labels = tuple of numbers
     *
     * @var array
     */
    private $row = array();

    /**
     * @var int
     */
    private $sizeX;

    /**
     * @var int
     */
    private $sizeY;

    public function setCol(array $labels)
    {
        $this->col = $labels;
        $this->sizeX = count($labels);
    }

    public function setRow(array $labels)
    {
        $this->row = $labels;
        $this->sizeY = count($labels);
    }

    public function getCol()
    {
        return $this->col;
    }

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
        if ($horizontal) {
            return $this->row[$index-1];
        }
        return $this->col[$index-1];
    }

    /**
     * required for drawing the field, how much space to leave blank for numbers
     * @return int
     */
    public function getMaxAmountVertical()
    {
        return $this->getMaxAmount(false);
    }

    /**
     * required for drawing the field, how much space to leave blank for numbers
     * @return int
     */
    public function getMaxAmountHorizontal()
    {
        return $this->getMaxAmount(true);
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
