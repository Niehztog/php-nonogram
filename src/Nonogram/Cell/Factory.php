<?php

namespace Nonogram\Cell;

class Factory
{
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
