<?php

namespace Nonogram\LevelParser;

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
        throw new Exception('Class ' . __CLASS__ . ' must implement method getGrid');
    }

    /**
     * Returns the char representing a Box in the grid
     * @return string
     */
    abstract protected function getBoxChar();

    /**
     * @param $char
     * @return \Nonogram\Cell\CellBox|\Nonogram\Cell\CellEmpty
     */
    protected function convertRawToCell($char)
    {
        $this->cellFactory->setStatusHidden(false);
        if ($this->getBoxChar() === $char) {
            $cell = $this->cellFactory->getBox();
        } else {
            $cell = $this->cellFactory->getEmpty();
        }
        return $cell;
    }

}
