<?php

namespace Nonogram\Grid\Provider;

class GridProviderFile implements AnyGridProvider
{
    private $rawData;

    /**
     * @param $fileName
     */
    public function load($fileName)
    {
        $raw = file_get_contents($fileName);
        if (false === $raw) {
            throw new \RuntimeException(sprintf('File %s not found', $fileName));
        }
        $this->rawData = $raw;
    }

    /**
     * @return array
     */
    public function provide()
    {
        $field = array();
        $raw = trim($this->rawData, " \r\n");
        $split = preg_split("/(\r\n|\n|\r)/", $raw);
        foreach ($split as $indexY => $row) {
            foreach (str_split($row) as $indexX => $char) {
                $cell = $this->convertRawToCell($char);
                $field[$indexY][$indexX] = $cell;
            }
        }
        return $field;
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
