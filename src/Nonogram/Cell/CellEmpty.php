<?php

namespace Nonogram\Cell;

use Nonogram\Exception\CellEmptyException;

class CellEmpty extends AbstractCell implements AnyCell
{

    /**
     * The cell's visible status
     * @var int
     */
    private $status = self::STATUS_HIDDEN;

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
        return self::TYPE_EMPTY;
    }

    /**
     * Provides the current visible status of the cell
     * required for rendering:
     * self::STATUS_DEFAULT
     * self::STATUS_HIDDEN
     * self::STATUS_MARKED
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Reveal cell, transforms to "box"
     * Transaction may fail with an Exception in case type is "empty"
     *
     * @return mixed
     * @throws CellEmptyException
     */
    public function fill()
    {
        throw new CellEmptyException();
    }

    /**
     * Mark cell as "empty"
     *
     * @return mixed
     */
    public function mark()
    {
        $this->status = self::STATUS_MARKED;
    }

    /*
     * Indicated whether the cell is marked as a box if its a box
     * and if its not marked as a box if its actually empty
     * @return bool
     */
    public function isSolved()
    {
        return self::STATUS_DEFAULT !== $this->getStatus();
    }

}
