<?php

namespace Nonogram\Label\Color;

class Color
{

    /**
     * @var
     */
    private $name;

    /**
     * @var
     */
    private $hex;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getHex()
    {
        return $this->hex;
    }

    /**
     * @param mixed $hex
     */
    public function setHex($hex)
    {
        $this->hex = $hex;
    }

}