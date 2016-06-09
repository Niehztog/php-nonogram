<?php

namespace Nonogram\LevelParser;

interface LevelParserInterface
{

    public function setRawData($rawData);

    /**
     * @param string $urn
     * @return bool
     */
    public function canHandle($urn);

}