<?php

namespace Nonogram\LevelParser;

class LevelParserDat extends AbstractLevelParserGrid implements LevelParserInterface
{

    /**
     * @var string
     */
    const DEFAULT_CHAR_EMPTY = '0';

    /**
     * @var string
     */
    const DEFAULT_CHAR_BOX = '1';

    /**
     * @var string
     */
    private $charEmpty = self::DEFAULT_CHAR_EMPTY;

    /**
     * @var string
     */
    private $charBox = self::DEFAULT_CHAR_BOX;

    /**
     * @var \Nonogram\Label\Color\Factory
     */
    private $colorFactory;

    /**
     * @var bool
     */
    private $useColors = false;

    /**
     * LevelParserDat constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     */
    public function __construct(
        \Nonogram\Label\Factory $labelFactory,
        \Nonogram\Cell\Factory $cellFactory,
        \Nonogram\Label\Color\Factory $colorFactory = null
    ) {
        if($colorFactory instanceof \Nonogram\Label\Color\Factory) {
            $this->colorFactory = $colorFactory;
        }
        parent::__construct($labelFactory, $cellFactory);
    }

    /**
     * Configuration setting, tells the parser which char
     * represents which cell type
     */
    public function useColors($bool)
    {
        $this->useColors = (bool) $bool;
    }

    /**
     * @param $charEmpty
     * @param $charBox
     */
    public function overrideChars($charEmpty = self::DEFAULT_CHAR_EMPTY, $charBox = self::DEFAULT_CHAR_BOX)
    {
        if(1 !== strlen($charEmpty) || 1 !== strlen($charBox)) {
            throw new \InvalidArgumentException('wrong char specification');
        }
        $this->charBox = $charBox;
        $this->charEmpty = $charEmpty;
    }

    /**
     * @return \Nonogram\Cell\AnyCell[][]
     */
    public function getGrid()
    {
        $field = array();
        $raw = trim($this->rawData, (' ' === $this->getCharEmpty()?'':' ')."\r\n");
        $split = preg_split("/(\r\n|\n|\r)/", $raw);
        foreach ($split as $indexY => $row) {
            if(0 === preg_match('~^['.preg_quote(implode('', $this->getCharList())).']+$~', $row)) {
                continue;
            }

            foreach (str_split($row) as $indexX => $char) {
                $cell = $this->getCellByChar($char);
                $field[$indexY][$indexX] = $cell;
            }
        }
        return array_values($field);
    }

    /**
     * This method returns the supported file extension
     * @return string
     */
    public function getFileExtension()
    {
        return 'dat';
    }

    /**
     * @param $char
     * @return \Nonogram\Cell\CellBox|\Nonogram\Cell\CellEmpty
     */
    private function getCellByChar($char)
    {
        $color = $this->colorFactory instanceof \Nonogram\Label\Color\Factory ? $this->colorFactory->getFromChar($char) : null;
        return parent::convertRawToCell($char, $color);
    }

    private function getCharList()
    {
        return $this->useColors ? $this->colorFactory->getCharList() : array($this->charBox, $this->charEmpty);
    }

    /**
     * Returns the char representing an empty cell in the grid
     * @return string
     */
    protected function getCharEmpty()
    {
        return $this->useColors ? $this->colorFactory->getFromHex('ffffff')->getDefaultChar() : $this->charEmpty;
    }

}
