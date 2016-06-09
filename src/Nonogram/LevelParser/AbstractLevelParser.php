<?php

namespace Nonogram\LevelParser;

abstract class AbstractLevelParser implements LevelParserInterface
{
    
    protected $rawData;
    
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * @return mixed
     */
    protected function getExtension($urn)
    {
        return pathinfo($urn, PATHINFO_EXTENSION);
    }

}
