<?php

namespace Nonogram\Label\Color;

class Color
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $hex;

    /**
     * @var string
     */
    private $defaultChar;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getHex()
    {
        return $this->hex;
    }

    /**
     * @param string $hex
     */
    public function setHex($hex)
    {
        $this->hex = $hex;
    }

    /**
     * @return string
     */
    public function getDefaultChar()
    {
        return $this->defaultChar;
    }

    /**
     * @param string $defaultChar
     */
    public function setDefaultChar($defaultChar)
    {
        $this->defaultChar = $defaultChar;
    }

}