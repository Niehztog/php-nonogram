<?php

namespace Nonogram\View;

/**
 * Interface ViewInterface
 * @package Nonogram\View
 */
interface ViewInterface {

    /**
     * @param $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function drawField();

    /**
     * @param \Nonogram\Grid\Grid $grid
     */
    public function setGrid(\Nonogram\Grid\Grid $grid);
    
}