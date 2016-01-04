<?php

namespace Nonogram\Solver;

class Solver
{

    /**
     * @param \Nonogram\Label\Provider\AnyLabelProvider $labelProvider
     * @return array
     */
    public function solve(\Nonogram\Label\Provider\AnyLabelProvider $labelProvider)
    {
        $labelsCol = $labelProvider->getLabelsForColumn();
        $labelsRow = $labelProvider->getLabelsForRow();

        $sizeX = count($labelsCol);
        $sizeY = count($labelsRow);

        $f = new \Nonogram\Cell\Factory();
        $field = array();
        //TODO:
        for($indexX = 0;$indexX < $sizeX; $indexX++) {
            for($indexY = 0;$indexY < $sizeY; $indexY++) {
                $field[$indexY][$indexX] = $f->getUnknown();
            }
        }

        return $field;
    }

}