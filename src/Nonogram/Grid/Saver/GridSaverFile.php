<?php

namespace Nonogram\Grid\Saver;

use Nonogram\Cell\AnyCell;
use Symfony\Component\Yaml\Dumper;

class GridSaverFile
{

    /**
     * Format for storing only block information without labels
     * (labels will be reconstructed upon loading)
     *
     * @var int
     */
    const FILE_FORMAT_DAT = 1;

    /**
     * Format for storing only label (and no block) information in
     * yaml format.
     *
     * @var int
     */
    const FILE_FORMAT_YAML = 2;

    /**
     * @var \Nonogram\Grid\Grid
     */
    private $grid;

    /**
     * Defines the file format for writing
     * @var
     */
    private $format;

    /**
     * @param $fileName
     */
    public function save($fileName)
    {
        $data = $this->format == self::FILE_FORMAT_YAML ? $this->getAsYaml() : $this->getAsDat();

        $res = file_put_contents($fileName, $data);
        if (false === $res) {
            throw new \RuntimeException(sprintf('File %s not written', $fileName));
        }
    }

    private function getAsYaml()
    {
        $dumper = new Dumper();
        $labels = $this->grid->getLabels();
        $data = array('columns' => $labels->getCol(), 'rows' => $labels->getRow());
        $yaml = $dumper->dump($data,2);
        return $yaml;
    }

    private function getAsDat()
    {
        $outStr = '';
        $field = $this->grid->getCells();
        foreach($field as $row) {
            foreach($row as $cell) {
                $outStr .= $cell->getType() === AnyCell::TYPE_BOX ? '1' : '0';
            }
            $outStr .= PHP_EOL;
        }
    }

    /**
     * Setter method for raw data
     *
     * @return array
     */
    public function setGrid(\Nonogram\Grid\Grid $grid)
    {
        $this->grid = $grid;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @param $char
     * @return \Nonogram\Cell\CellBox|\Nonogram\Cell\CellEmpty
     */
    private function convertRawToCell($char)
    {
        if ('1' === $char) {
            $cell = new \Nonogram\Cell\CellBox();
        } else {
            $cell = new \Nonogram\Cell\CellEmpty();
        }
        return $cell;
    }

}