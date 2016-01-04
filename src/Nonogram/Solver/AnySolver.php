<?php

namespace Nonogram\Solver;

interface AnySolver
{

    /**
     * @param \Nonogram\Label\Provider\AnyLabelProvider $labelProvider
     * @return array
     */
    public function solve(\Nonogram\Label\Provider\AnyLabelProvider $labelProvider);

}