<?php

namespace Nonogram\Cell;

use Nonogram\Exception\CellEmptyException;

interface AnyCell
{

    /**
     * Indicates the actual cell trait (unknown)
     * @var int
     */
    const TYPE_UNKNOWN = 0;

    /**
     * Indicates the actual cell trait (box)
     * @var int
     */
    const TYPE_BOX = 1;

    /**
     * Indicates the actual cell trait (empty)
     * @var int
     */
    const TYPE_EMPTY = 2;

    /**
     * Indicates the actual cell trait (box?)
     * @var int
     */
    const STATUS_DEFAULT = 0;

    /**
     * Indicates the current cell state (hidden/unknown)
     * @var int
     */
    const STATUS_HIDDEN = 1;

    /**
     * Indicates the current cell state (marked as empty)
     * @var int
     */
    const STATUS_MARKED = 2;

    /**
     * Should return one of the following
     * self::TYPE_BOX
     * self::TYPE_EMPTY
     *
     * referring to the type of the cell
     *
     * @return mixed
     */
    public function getType();

    /**
     * Provides the current visible status of the cell
     * required for rendering:
     * self::STATUS_DEFAULT
     * self::STATUS_HIDDEN
     * self::STATUS_MARKED
     *
     * @return mixed
     */
    public function getStatus();

    /**
     * Reveal cell, transforms to "box"
     * Transaction may fail with an Exception in case type is "empty"
     *
     * @return mixed
     * @throws CellEmptyException
     */
    public function fill();

    /**
     * Mark cell as "empty"
     *
     * @return mixed
     */
    public function mark();

    /*
     * Indicated whether the cell is marked as a box if its a box
     * and if its not marked as a box if its actually empty
     * @return bool
     */
    public function isSolved();

    /**
     * @return \Nonogram\Label\Color\Color
     */
    public function getColor();

    /**
     * @param \Nonogram\Label\Color\Color $color
     */
    public function setColor(\Nonogram\Label\Color\Color $color);

}
