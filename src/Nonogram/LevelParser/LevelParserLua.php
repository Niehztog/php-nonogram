<?php

namespace Nonogram\LevelParser;

class LevelParserLua extends AbstractLevelParserGrid implements LevelParserInterface
{

    /**
     * @var string
     */
    const EMPTY_CHAR = '.';

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
     * Returns the char representing an empty cell in the grid
     * @return string
     */
    protected function getCharEmpty()
    {
        return self::EMPTY_CHAR;
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
