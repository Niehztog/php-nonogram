<?php

namespace Nonogram\View;

use Nonogram\Cell\AnyCell;

/**
 * Class ViewTextMinimal
 * @package Nonogram\View
 */
class ViewTextMinimal extends AbstractView implements ViewInterface, ViewWritableInterface {

    /**
     * @return string
     */
    public function drawField() {
        $outStr = '';
        $field = $this->grid->getCells();
        foreach($field as $row) {
            foreach($row as $cell) {
                $outStr .= $cell->getType() === AnyCell::TYPE_BOX ? '1' : '0';
            }
            $outStr .= PHP_EOL;
        }
        return $outStr;
    }

    /**
     * In case output format supports being written to a file, this method returns the suitable file extension
     * @return string
     */
    public function getFileExtension()
    {
        return 'dat';
    }

}