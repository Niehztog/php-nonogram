<?php

namespace Nonogram\Label;

class Count
{

    /**
     * @var int
     */
    private $number;

    /**
     * @var \Nonogram\Label\Color\Color
     */
    private $color;

    /**
     * Count constructor.
     * @param $number
     * @param int $color
     */
    public function __construct($number, \Nonogram\Label\Color\Color $color)
    {
        $this->setNumber($number);
        $this->setColor($color);
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param \Nonogram\Label\Color\Color $color
     * @throws \InvalidArgumentException
     */
    public function setColor(\Nonogram\Label\Color\Color $color)
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getNumber();
    }

}