<?php

namespace Nonogram\Cell;

class CellWrapper implements AnyCell
{
    /**
     * Wrapped object
     * @var AnyCell|null
     */
    private $cellInstance;

    /**
     * Constructor
     * accepts parameter optionally, in case of yet unknown cell traits
     *
     * @param AnyCell|null $cell
     */
    public function __construct(AnyCell $cell = null)
    {
        $this->cellInstance = $cell;
    }

    /**
     * Should return one of the following
     * self::TYPE_UNKNOWN
     * self::TYPE_BOX
     * self::TYPE_EMPTY
     *
     * referring to the type of the cell
     *
     * @return mixed
     */
    public function getType()
    {
        return null === $this->cellInstance ? self::TYPE_UNKNOWN : $this->cellInstance->getType();
    }

    /**
     * Provides the current visible status of the cell
     * required for rendering
     *
     * @return mixed
     */
    public function getStatus()
    {
        if (null === $this->cellInstance) {
            return self::STATUS_HIDDEN;
        }
        return $this->cellInstance->getStatus();
    }

    /**
     * Reveal cell, transforms to "box"
     * Transaction may fail with an Exception in case type is "empty"
     *
     * @return mixed
     * @throws \Exception
     */
    public function fill()
    {
        return $this->cellInstance->fill();
    }

    /**
     * Mark cell as "empty"
     *
     * @return mixed
     */
    public function mark()
    {
        return $this->cellInstance->mark();
    }

    /*
     * Indicated whether the cell is marked as a box if its a box
     * and if its not marked as a box if its actually empty
     * @return bool
     */
    public function isSolved()
    {
        return null !== $this->cellInstance && $this->cellInstance->isSolved();
    }

    /**
     * @return \Nonogram\Label\Color\Color
     */
    public function getColor()
    {
        return $this->cellInstance->getColor();
    }

    /**
     * @param \Nonogram\Label\Color\Color $color
     */
    public function setColor(\Nonogram\Label\Color\Color $color)
    {
        $this->cellInstance->setColor($color);
    }

}
