<?php

namespace Nonogram\Cell;

class Factory implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    /**
     * @var bool
     */
    private $setStatusHidden = true;

    /**
     * @param boolean $setStatusHidden
     */
    public function setStatusHidden($setStatusHidden)
    {
        $this->setStatusHidden = (bool)$setStatusHidden;
    }

    /**
     * @param $type
     * @return CellWrapper
     */
    public function getByType($type)
    {
        switch($type) {
            case AnyCell::TYPE_BOX:
                return $this->getBox();
            case AnyCell::TYPE_EMPTY:
                return $this->getEmpty();
        }
        return $this->getUnknown();
    }

    /**
     * @param \Nonogram\Label\Color\Color|null $color
     * @return CellWrapper
     */
    public function getBox(\Nonogram\Label\Color\Color $color = null)
    {
        $cell = $this->container->get('cell_box');
        if(null !== $color) {
            $cell->setColor($color);
        }
        if(!$this->setStatusHidden) {
            $cell->fill();
        }
        return $cell;
    }

    /**
     * @param \Nonogram\Label\Color\Color|null $color
     * @return CellWrapper
     */
    public function getEmpty(\Nonogram\Label\Color\Color $color = null)
    {
        $cell = $this->container->get('cell_empty');
        if(null !== $color) {
            $cell->setColor($color);
        }
        if(!$this->setStatusHidden) {
            $cell->mark();
        }
        return $cell;
    }

    /**
     * @return CellWrapper
     */
    public function getUnknown()
    {
        return $this->container->get('cell_wrapper');
    }
}
