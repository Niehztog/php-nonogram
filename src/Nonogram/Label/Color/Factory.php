<?php

namespace Nonogram\Label\Color;

class Factory implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    /**
     * @var array
     */
    private $instances = array();

    /**
     * @param Color $color
     */
    public function addColor(\Nonogram\Label\Color\Color $color) {
        $this->instances[] = $color;
    }

    /**
     * @return array
     */
    public function getCharList()
    {
        $list = array();
        foreach($this->instances as $color) {
            $list[] = $color->getDefaultChar();
        }
        return $list;
    }

    /**
     * @param string $hex
     * @return \Nonogram\Label\Color\Color
     */
    public function getFromHex($hex) {
        return $this->findByProperty($hex, 'getHex');
    }

    /**
     * @param string $char
     * @return \Nonogram\Label\Color\Color
     */
    public function getFromChar($char) {
        return $this->findByProperty($char, 'getDefaultChar');
    }

    private function findByProperty($value, $getter)
    {
        foreach($this->instances as $color) {
            if($value === $color->$getter()) {
                return $color;
            }
        }
        return reset($this->instances);
    }

}