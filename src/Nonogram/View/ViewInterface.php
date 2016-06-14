<?php

namespace Nonogram\View;

/**
 * Interface ViewInterface
 * @package Nonogram\View
 */
interface ViewInterface {

    /**
     * @return string
     */
    public function drawField();

    /**
     * @param \Nonogram\Grid\Grid $grid
     */
    public function setGrid(\Nonogram\Grid\Grid $grid);

    /**
     * @return \Nonogram\Grid\Grid $grid
     */
    public function getGrid();
    
}