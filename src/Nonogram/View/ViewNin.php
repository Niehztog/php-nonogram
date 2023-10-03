<?php

namespace Nonogram\View;

/**
 * Class ViewNin
 * @package Nonogram\View
 */
class ViewNin extends AbstractView implements ViewInterface, ViewWritableInterface {

    /**
     * @return string
     */
    public function drawField() {
        $labels = $this->grid->getLabels();
        $data = array('rows' => $labels->getRow(), 'columns' => $labels->getCol());
        $out = count($data['columns']) . ' ' . count($data['rows']) . PHP_EOL;
        foreach($data as $direction) {
            foreach($direction as $sequence) {
                $out .= implode(' ', $sequence) . PHP_EOL;
            }
        }
        return $out;
    }

    /**
     * In case output format supports being written to a file, this method returns the suitable file extension
     * @return string
     */
    public function getFileExtension()
    {
        return 'nin';
    }

}