<?php

namespace Nonogram\LevelParser;

class LevelParserLua extends AbstractLevelParserGrid implements LevelParserInterface
{

    /**
     * @var string
     */
    const BOX_CHAR = 'O';

    /**
     * @return \Nonogram\Cell\AnyCell[][]
     */
    public function getGrid()
    {
        $field = [];
        $raw = trim($this->rawData, " \r\n");
        $split = preg_split("/(\r\n|\n|\r)/", $raw);
        $indexY = 0;
        foreach ($split as $row) {
            if (0 !== strpos($row, 'irow')) {
                continue;
            }
            preg_match_all('~\{(.+?)\}~', $row, $m);
            if (!isset($m[1][0])) {
                continue;
            }
            $parsedValues = str_getcsv($m[1][0], ',', '"');

            foreach ($parsedValues as $indexX => $char) {
                $cell = $this->convertRawToCell($char);
                $field[$indexY][$indexX] = $cell;
            }
            $indexY++;
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
        return 'lua';
    }
    
}
