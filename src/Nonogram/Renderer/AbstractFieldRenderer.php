<?php

namespace Nonogram\Renderer;

/**
 * Class AbstractFieldRenderer
 * @package Nonogram\Renderer
 */
abstract class AbstractFieldRenderer implements AnyFieldRenderer
{
    /**
     * Counts failed attempts to unveil actual empty cells as boxes
     * @var int
     */
    private $failureCounter = 0;

    /**
     * @var \Nonogram\Grid\Grid
     */
    protected $grid;

    /**
     * @param \Nonogram\Grid\Grid $grid
     */
    public function __construct($grid)
    {
        $this->grid = $grid;
    }

    /**
     *
     */
    protected function incFailureCounter()
    {
        $this->failureCounter++;
    }

    /**
     * @return int
     */
    protected function getFailureCounter()
    {
        return $this->failureCounter;
    }
}
