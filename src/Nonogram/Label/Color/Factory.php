<?php

namespace Nonogram\Label\Color;

class Factory implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    private $instances;

    public function addColor(\Nonogram\Label\Color\Color $color) {
        $this->instances[] = $color;
    }

    public function getFromHex($hex) {
        foreach($this->instances as $color) {
            if($hex === $color->getHex()) {
                return $color;
            }
        }
        return reset($this->instances);
    }

}