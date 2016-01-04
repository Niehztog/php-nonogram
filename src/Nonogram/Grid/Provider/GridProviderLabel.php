<?php

namespace Nonogram\Grid\Provider;
use Nonogram\Solver\Solver;

class GridProviderLabel implements AnyGridProvider
{
    private $data;

    /**
     * @param \Nonogram\Label\Provider\AnyLabelProvider $labelProvider
     */
    public function setLabels(\Nonogram\Label\Provider\AnyLabelProvider $labelProvider)
    {
        $solver = new Solver();
        $this->data = $solver->solve($labelProvider);
    }

    /**
     * @return array
     */
    public function provide()
    {
        return $this->data;
    }
}
