<?php

namespace Nonogram\Solver;

interface AnySolver
{
    /**
     * @param \Nonogram\Grid\Grid $grid
     */
    public function solve(\Nonogram\Grid\Grid $grid);

    /**
     * Getter for property "ruleActionCounter"
     *
     * @return array
     */
    public function getSolvingStatistics();

}
