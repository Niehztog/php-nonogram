<?php

namespace Nonogram\Solver;

abstract class AbstractSolver implements AnySolver
{

    /**
     * counts how many updates come from each rule
     * @var array
     */
    protected $solvingStatistics = array();

    /**
     * @param \Nonogram\Grid\Grid $grid
     */
    abstract public function solve(\Nonogram\Grid\Grid $grid);

    /**
     * Getter for property "ruleActionCounter"
     *
     * @return array
     */
    public function getSolvingStatistics()
    {
        return $this->solvingStatistics;
    }

}
