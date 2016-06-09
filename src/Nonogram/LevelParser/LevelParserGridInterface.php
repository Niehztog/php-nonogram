<?php

namespace Nonogram\LevelParser;

interface LevelParserGridInterface extends LevelParserInterface
{

    /**
     * @return \Nonogram\Cell\AnyCell[][]
     */
    public function getGrid();

}