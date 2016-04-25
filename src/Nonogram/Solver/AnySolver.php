<?php

namespace Nonogram\Solver;

interface AnySolver
{
    /**
     * @param \Nonogram\Label\Label $labels
     * @return array
     */
    public function solve(\Nonogram\Label\Label $labels);
}
