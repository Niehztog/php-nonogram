<?php

namespace Nonogram\Label;

/**
 * Class LabelProviderCells
 * @package Nonogram\Label
 */
class LabelProviderCells
{
    /**
     * @var array $grid
     */
    private $cells;

    /**
     * @param array $cells
     * @return array
     */
    public function generateLabels(array $cells)
    {
        $this->cells = $cells;
        $label = [];
        if(count($cells) > 1) {
            $label['columns'] = $this->getLabelsForColumn();
        }
        $label['rows'] = $this->getLabelsForRow();
        return $label;
    }

    /**
     * @return array
     */
    private function getLabelsForColumn()
    {
        return $this->getLabels(false);
    }

    /**
     * @return array
     */
    private function getLabelsForRow()
    {
        return $this->getLabels(true);
    }

    /**
     * @param $horizontal
     * @return array
     */
    private function getLabels($horizontal)
    {
        $sizeX = count($this->cells[0]);
        $sizeY = count($this->cells);
        $labelsAll = [];
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
            $labelSequence = array(0);
            for ($$innerIndex=1;$$innerIndex<=$$innerLimit;$$innerIndex++) {
                if(!isset($this->cells[$y-1][$x-1])) {
                    throw new \RuntimeException('Undefined offset: '.$x.'/'.$y);
                }
                $cell = $this->cells[$y-1][$x-1];
                end($labelSequence);
                $key = key($labelSequence);

                if ($cell::TYPE_BOX === $cell->getType()) {
                    $labelSequence[$key]++;
                } elseif (null!==$key && $labelSequence[$key] > 0) {
                    $labelSequence[] = 0;
                }
            }
            if (0 === end($labelSequence) && 1 !== count($labelSequence)) {
                array_pop($labelSequence);
            }
            $labelsAll[] = $labelSequence;
        }

        return $labelsAll;
    }
}
