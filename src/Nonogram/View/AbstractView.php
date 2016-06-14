<?php

namespace Nonogram\View;

/**
 * Class AbstractView
 * @package Nonogram\View
 */
abstract class AbstractView implements ViewInterface
{
    /**
     * @var \Nonogram\Grid\Grid
     */
    protected $grid;

    /**
     * @param \Nonogram\Grid\Grid $grid
     */
    public function setGrid(\Nonogram\Grid\Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @return \Nonogram\Grid\Grid $grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * Indicates whether View supports storing multiple puzzles in one instance or just one
     * @return boolean
     */
    public function supportsMultiple()
    {
        return false;
    }

}
