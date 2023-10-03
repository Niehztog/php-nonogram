<?php

namespace Nonogram\LevelParser;

class LevelParserDat extends AbstractLevelParserGrid implements LevelParserInterface
{

    /**
     * @var string
     */
    const BOX_CHAR = '1';
    
    /**
     * @return \Nonogram\Cell\AnyCell[][]
     */
    public function getGrid()
    {
        $field = [];
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
     * Returns the char representing a Box in the grid
     * @return string
     */
    protected function getBoxChar()
    {
        return self::BOX_CHAR;
    }

    /**
     * This method returns the supported file extension
     * @return string
     */
    public function getFileExtension()
    {
        return 'dat';
    }

}
