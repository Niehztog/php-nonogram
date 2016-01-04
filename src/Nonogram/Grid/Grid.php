<?php

namespace Nonogram\Grid;

/**
 * Class Grid
 * @package Nonogram\Grid
 */
class Grid
{
    /**
     * 2-dimensional array
     * 1st dimension corresponds to the y-coordinate
     * 2nd dimension corresponds to the x-coordinate
     *
     * the smallest coordinate x- and y-wise is located in the upper left corner
     * the largest coordinate x- and y-wise is located in the lower right corner
     *
     * @var array
     */
    private $cells = array();

    /**
     * Numeric row labels = tuple of numbers
     *
     * @var array
     */
    private $labelsRow = array();

    /**
     * Numeric column labels = tuple of numbers
     *
     * @var array
     */
    private $labelsColumn = array();

    /**
     * @param \Nonogram\Grid\Provider\AnyGridProvider $provider
     */
    public function setCells(\Nonogram\Grid\Provider\AnyGridProvider $provider)
    {
        $this->cells = $provider->provide();
    }

    /**
     * @param \Nonogram\Grid\Provider\AnyGridProvider $provider
     */
    public function setLabels(\Nonogram\Label\Provider\AnyLabelProvider $provider)
    {
        $this->labelsColumn = $provider->getLabelsForColumn();
        $this->labelsRow = $provider->getLabelsForRow();
    }

    /**
     *
     *
     * @param $x
     * @param $y
     * @return mixed
     */
    public function getCell($x, $y)
    {
        if (!isset($this->cells[$y-1][$x-1])) {
            throw new \OutOfRangeException(sprintf('No cell at %d:%d', $x, $y));
        }
        return $this->cells[$y-1][$x-1];
    }

    /**
     * Returns the number of columns (horizontal size)
     *
     * required for drawing the field
     */
    public function getSizeX()
    {
        return count(reset($this->cells));
    }

    /**
     * Returns the number of rows (vertical size)
     *
     * required for drawing the field
     */
    public function getSizeY()
    {
        return count($this->cells);
    }

    /**
     * Tells whether all cells are unveiled to their true state
     * @return bool
     */
    public function isSolved()
    {
        foreach ($this->cells as $y => $row) {
            foreach($row as $x => $cell) {
                if(!$cell->isSolved()) {
                    return false;
                }
            }
        }
        return true;
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
     * @param $index
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
            return $this->labelsRow[$index-1];
        }
        return $this->labelsColumn[$index-1];
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
        $labelVariable = $horizontal ? 'labelsRow' : 'labelsColumn';
        $max = 0;
        foreach ($this->$labelVariable as $labels) {
            $count = count($labels);
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }
}
