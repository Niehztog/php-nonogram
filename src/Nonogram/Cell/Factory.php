<?php

namespace Nonogram\Cell;

class Factory
{
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

    public function getBox()
    {
        return new CellWrapper(new CellBox());
    }

    public function getEmpty()
    {
        return new CellWrapper(new CellEmpty());
    }

    public function getUnknown()
    {
        return new CellWrapper(null);
    }
}
