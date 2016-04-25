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
     * @var \Nonogram\Label\Label
     */
    private $labels;

    /**
     * @var array
     */
    private $solvingStatistics;

    /**
     * @param \Nonogram\Grid\Provider\AnyGridProvider $provider
     */
    public function setCells(\Nonogram\Grid\Provider\AnyGridProvider $provider)
    {
        $this->cells = $provider->provide();
        if(method_exists($provider,'getSolvingStatistics')) {
            $this->solvingStatistics = $provider->getSolvingStatistics();
        }
    }

    /**
     * @param \Nonogram\Label\Label $label
     */
    public function setLabels(\Nonogram\Label\Label $label)
    {
        $this->labels = $label;
    }

    /**
     * @return \Nonogram\Label\Label
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Getter for all cells
     * @return AnyCell[][]
     */
    public function getCells()
    {
        return $this->cells;
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
            foreach ($row as $x => $cell) {
                if (!$cell->isSolved()) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getSolvingStatistics()
    {
        return $this->solvingStatistics;
    }

}
