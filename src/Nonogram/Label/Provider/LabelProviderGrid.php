<?php

namespace Nonogram\Label\Provider;

/**
 * Class LabelProviderGrid
 * @package Nonogram\Grid
 */
class LabelProviderGrid implements AnyLabelProvider
{
    /**
     * @var \Nonogram\Grid\Grid $grid
     */
    private $grid;

    /**
     * LabelGenerator constructor.
     *
     * @param \Nonogram\Grid\Grid $grid
     */
    public function __construct(\Nonogram\Grid\Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @return array
     */
    public function getLabelsForColumn()
    {
        return $this->getLabels(false);
    }

    /**
     * @return array
     */
    public function getLabelsForRow()
    {
        return $this->getLabels(true);
    }

    /**
     * @param $horizontal
     * @return array
     */
    private function getLabels($horizontal)
    {
        $sizeX = $this->grid->getSizeX();
        $sizeY = $this->grid->getSizeY();
        $labelsAll = array();
        if ($horizontal) {
            $outerLimit = 'sizeY';
            $innerLimit = 'sizeX';
            $outerIndex = 'y';
            $innerIndex = 'x';
        } else {
            $outerLimit = 'sizeX';
            $innerLimit = 'sizeY';
            $outerIndex = 'x';
            $innerIndex = 'y';
        }

        for ($$outerIndex=1;$$outerIndex<=$$outerLimit;$$outerIndex++) {
            $labelsCol = array();
            for ($$innerIndex=1;$$innerIndex<=$$innerLimit;$$innerIndex++) {
                $cell = $this->grid->getCell($x, $y);
                end($labelsCol);
                $key = key($labelsCol);

                if ($cell::TYPE_BOX === $cell->getType()) {
                    if (null===$key) {
                        $labelsCol[] = 0;
                        $key = key($labelsCol);
                    }
                    $labelsCol[$key]++;
                } else {
                    if (null!==$key && $labelsCol[$key] > 0) {
                        $labelsCol[] = 0;
                    }
                }
            }
            if (0 === end($labelsCol)) {
                array_pop($labelsCol);
            }
            $labelsAll[] = $labelsCol;
        }

        return $labelsAll;
    }
}
