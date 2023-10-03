<?php

namespace Nonogram\LevelParser;

/**
 * Abstract Class for parsers which can interpret and provide grid layouts additional to
 * label/clue information
 *
 * @package Nonogram\LevelParser
 */
abstract class AbstractLevelParserGrid extends AbstractLevelParser implements LevelParserInterface
{
    /**
     * @var \Nonogram\Cell\Factory
     */
    protected $cellFactory;

    /**
     * LevelParserDat constructor.
     * @param \Nonogram\Cell\Factory $cellFactory
     */
    public function __construct(\Nonogram\Label\Factory $labelFactory, \Nonogram\Cell\Factory $cellFactory)
    {
        $this->cellFactory = $cellFactory;
        parent::__construct($labelFactory);
    }

    /**
     * Returns the grid information - if not available
     * should be generated out of the grid information
     *
     * @return Label
     */
    public function getLabels()
    {
        return $this->labelFactory->getForCells($this->getGrid());
    }

    /**
     * Returns the grid information - if available
     * must be overridden by child implementation
     *
     * @return \Nonogram\Cell\AnyCell[][]
     */
    public function getGrid()
    {
        throw new \Exception('Class ' . __CLASS__ . ' must implement method getGrid');
    }

    /**
     * Some formats MAY provide grids, those can override this method to determine
     * its ability to provide grids dynamically
     * @return bool
     */
    public function hasGrid()
    {
        return true;
    }

    /**
     * Returns the char representing an empty cell in the grid
     * @return string
     */
    abstract protected function getCharEmpty();

    /**
     * @param $char
     * @param \Nonogram\Label\Color\Color|null $color
     * @return \Nonogram\Cell\CellWrapper
     */
    protected function convertRawToCell($char, \Nonogram\Label\Color\Color $color = null)
    {
        $this->cellFactory->setStatusHidden(false);
        if ($this->getCharEmpty() === $char) {
            $cell = $this->cellFactory->getEmpty($color);
        } else {
            $cell = $this->cellFactory->getBox($color);
        }
        return $cell;
    }

}
