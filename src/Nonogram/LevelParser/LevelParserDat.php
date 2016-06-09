<?php

namespace Nonogram\LevelParser;

class LevelParserDat extends AbstractLevelParser implements LevelParserGridInterface
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
        return 'dat' === $this->getExtension($urn);
    }

    /**
     * @return \Nonogram\Cell\AnyCell[][]
     */
    public function getGrid()
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
    {$this->cellFactory->setStatusHidden(false);
        if ('1' === $char) {
            $cell = $this->cellFactory->getBox();
        } else {
            $cell = $this->cellFactory->getEmpty();
        }
        return $cell;
    }
    
}
