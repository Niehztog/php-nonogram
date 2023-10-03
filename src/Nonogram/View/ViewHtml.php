<?php

namespace Nonogram\View;

use Nonogram\Cell\AnyCell;

/**
 * Class ViewHtml
 * @package Nonogram\View
 */
class ViewHtml extends AbstractView implements ViewInterface, ViewWritableInterface {

    /**
     * Determines whether to display the solution or draw the nonogram as unsolved puzzle
     * @var bool
     */
    private $solved = true;

    /**
     * Size in pixels of cell sides (width and high)
     * @var int
     */
    private $cellSizePx = 15;

    /**
     * Indicates whether View should store multiple puzzles in one output or just one
     * @var bool
     */
    private $exportMultiple = false;

    /**
     * @param \Nonogram\Grid\Grid $grid
     */
    public function setGrid(\Nonogram\Grid\Grid $grid)
    {
        if(!$this->supportsMultiple()) {
            $this->grid = [];
        }
        $this->grid[] = $grid;
    }

    /**
     * @return string
     */
    public function drawField() {
        $outStr = '<html><head><title>'.$this->grid[0]->getTitle().'</title>' . PHP_EOL;
        $outStr .= '<style>' . PHP_EOL;
        $outStr .= 'div {' . PHP_EOL;
        $outStr .= '    font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;' . PHP_EOL;
        $outStr .= '    font-size: 12px;' . PHP_EOL;
        $outStr .= '    font-style: normal;' . PHP_EOL;
        $outStr .= '    font-variant: normal;' . PHP_EOL;
        $outStr .= '    font-weight: 400;' . PHP_EOL;
        $outStr .= '    line-height: '.($this->cellSizePx - 2).'px;' . PHP_EOL;
        $outStr .= '}' . PHP_EOL;
        $outStr .= 'div {box-sizing:border-box;margin: 0;padding: 0;text-align:center;}' . PHP_EOL;
        $outStr .= 'div.container {display:flex; flex-direction:row; flex-wrap:wrap;}' . PHP_EOL;
        $outStr .= 'div.container2 {display:flex;flex-wrap: nowrap;flex-direction: row;align-items:flex-end;padding-bottom:'.$this->cellSizePx.'px;padding-right:'.$this->cellSizePx.'px;}' . PHP_EOL;
        $outStr .= 'div.container3 {display:flex;flex-wrap: nowrap;flex-direction: column;align-items:flex-end;}' . PHP_EOL;
        $outStr .= 'div.container3 > div.grid {display:flex;flex-direction:column;}' . PHP_EOL;
        $outStr .= 'div.container3 > div.grid > div.row {display:flex;flex-direction:row;}' . PHP_EOL;
        $outStr .= 'div.container3 > div.grid > div.row > div.cell {border: 1px black solid;width:'.$this->cellSizePx.'px; height:'.$this->cellSizePx.'px;}' . PHP_EOL;
        $outStr .= 'div.container3 > div.grid > div.row > div.cell:nth-child(5n) {border-right: 2px black solid;}' . PHP_EOL;
        $outStr .= 'div.container3 > div.grid > div.row:nth-child(5n) > div.cell {border-bottom: 2px black solid;}' . PHP_EOL;
        $outStr .= 'div.container3 > div.grid > div.row > div.cell:nth-child(5n+1) {border-left: 2px black solid;}' . PHP_EOL;
        $outStr .= 'div.container3 > div.grid > div.row:nth-child(5n+1) > div.cell {border-top: 2px black solid;}' . PHP_EOL;
        $outStr .= 'div.container3 > div.grid > div.row > div.cell.block {background-color: black;}' . PHP_EOL;
        $outStr .= 'div.container3 > div.grid > div.row > div.cell.empty:before {content: "X";font-size:18px;}' . PHP_EOL;
        $outStr .= 'div.container3 > div.columnlabels {display:flex;align-items:flex-end;}' . PHP_EOL;
        $outStr .= 'div.container3 > div.columnlabels > div.cell {border-left:1px black solid;border-bottom:1px black solid;border-right:1px black solid;width:'.$this->cellSizePx.'px;}' . PHP_EOL;
        $outStr .= 'div.container3 > div.columnlabels > div.cell:nth-child(5n) {border-right: 2px black solid;}' . PHP_EOL;
        $outStr .= 'div.container3 > div.columnlabels > div.cell:nth-child(5n+1) {border-left: 2px black solid;}' . PHP_EOL;
        $outStr .= 'div.container2 > div.rowlabels {display:flex;flex-direction:column;align-items:flex-end;}' . PHP_EOL;
        $outStr .= 'div.container2 > div.rowlabels > div.cell {padding-right:5px;border-top:1px black solid;border-bottom:1px black solid;border-right:1px black solid;height:'.$this->cellSizePx.'px;}' . PHP_EOL;
        $outStr .= 'div.container2 > div.rowlabels > div.cell:nth-child(5n) {border-bottom: 2px black solid;}' . PHP_EOL;
        $outStr .= 'div.container2 > div.rowlabels > div.cell:nth-child(5n+1) {border-top: 2px black solid;}' . PHP_EOL;
        $outStr .= '</style>' . PHP_EOL;
        $outStr .= '</head><body>' . PHP_EOL;

        $outStr .= '<div class="container">' . PHP_EOL;

        foreach($this->grid as $grid) {
            $outStr .= '<div class="container2">' . PHP_EOL;

            $labels = $grid->getLabels();
            $labelsColumns = $labels->getCol();
            $labelsRows = $labels->getRow();

            $outStr .= '<div class="rowlabels">' . PHP_EOL;
            //draw column labens
            foreach ($labelsRows as $labelsRow) {
                $outStr .= '<div class="cell">';
                $outStr .= implode('&nbsp;&nbsp;', $labelsRow);
                $outStr .= '</div>' . PHP_EOL;
            }
            $outStr .= '</div>' . PHP_EOL;

            $outStr .= '<div class="container3">' . PHP_EOL;
            $outStr .= '<div class="columnlabels">' . PHP_EOL;

            //draw column labens
            foreach ($labelsColumns as $labelsColumn) {
                $outStr .= '<div class="cell">';
                $outStr .= implode('<br/>', $labelsColumn);
                $outStr .= '</div>' . PHP_EOL;
            }
            $outStr .= '</div>' . PHP_EOL;

            $outStr .= '<div class="grid">' . PHP_EOL;

            $field = $grid->getCells();
            foreach ($field as $key => $row) {
                $outStr .= '<div class="row">' . PHP_EOL;
                foreach ($row as $cell) {
                    $outStr .= '<div class="cell ' . ($this->solved ? ($cell->getType() === AnyCell::TYPE_BOX ? 'block' : ($cell->getType() === AnyCell::TYPE_EMPTY ? 'empty' : '')) : '') . '">&nbsp;</div>';
                }
                $outStr .= '</div>' . PHP_EOL;
            }

            $outStr .= '</div>' . PHP_EOL;
            $outStr .= '</div>' . PHP_EOL;
            $outStr .= '</div>' . PHP_EOL;

        }

        $outStr .= '</div>' . PHP_EOL;
        $outStr .= '</body></html>';
        return $outStr;
    }

    /**
     * In case output format supports being written to a file, this method returns the suitable file extension
     * @return string
     */
    public function getFileExtension()
    {
        return 'html';
    }

    /**
     * Determines whether to display the solution or draw the nonogram as unsolved puzzle
     * @param $value
     */
    public function setSolved($value)
    {
        $this->solved = (bool)$value;
    }

    /**
     * @param mixed $cellSizePx
     */
    public function setCellSizePx($cellSizePx)
    {
        $this->cellSizePx = $cellSizePx;
    }

    /**
     * @param $value
     */
    public function setExportMultiple($value)
    {
        $this->exportMultiple = (bool)$value;
    }

    /**
     * Indicates whether View supports storing multiple puzzles in one instance or just one
     * @return boolean
     */
    public function supportsMultiple()
    {
        return $this->exportMultiple;
    }

}