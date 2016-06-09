<?php

namespace Nonogram\LevelParser;

interface LevelParserLabelInterface extends LevelParserInterface
{

    /**
     * @return Label
     */
    public function getLabels();

}