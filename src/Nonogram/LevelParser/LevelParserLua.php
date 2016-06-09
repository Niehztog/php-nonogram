<?php

namespace Nonogram\LevelParser;

class LevelParserLua extends AbstractLevelParser implements LevelParserGridInterface
{

    /**
     * @var \Nonogram\Cell\Factory
     */
    private $cellFactory;

    public function __construct(\Nonogram\Cell\Factory $cellFactory)
    {
        $this->cellFactory = $cellFactory;
    }

    /**
     * @param string $urn
     * @return bool
     */
    public function canHandle($urn)
    {
        return 'lua' === $this->getExtension($urn);
    }

    /**
     * @return \Nonogram\Cell\AnyCell[][]
     */
    public function getGrid()
    {
        $field = array();
        $raw = trim($this->rawData, " \r\n");
        $split = preg_split("/(\r\n|\n|\r)/", $raw);
        $indexY = 0;
        foreach ($split as $row) {
            if(0 !== strpos($row, 'irow')) {
                continue;
            }
            preg_match_all('~\{(.+?)\}~', $row, $m);
            if(!isset($m[1][0])) {
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
     * @param $char
     * @return \Nonogram\Cell\CellBox|\Nonogram\Cell\CellEmpty
     */
    private function convertRawToCell($char)
    {$this->cellFactory->setStatusHidden(false);
        if ('O' === $char) {
            $cell = $this->cellFactory->getBox();
        } else {
            $cell = $this->cellFactory->getEmpty();
        }
        return $cell;
    }

}
