<?php

namespace Nonogram\Cell;

abstract class AbstractCell implements \Nonogram\Cell\AnyCell
{

    /**
     * @var \Nonogram\Label\Color\Color
     */
    private $color;

    /**
     * @return \Nonogram\Label\Color\Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param \Nonogram\Label\Color\Color $color
     */
    public function setColor(\Nonogram\Label\Color\Color $color)
    {
        $this->color = $color;
    }

}