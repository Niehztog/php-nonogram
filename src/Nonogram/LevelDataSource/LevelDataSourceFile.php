<?php

namespace Nonogram\LevelDataSource;

class LevelDataSourceFile extends AbstractLevelDataSource implements LevelDataSourceInterface
{

    /**
     * @param string $urn
     * @return bool
     */
    public function canHandle($urn)
    {
        return file_exists($urn);
    }

    /**
     * @return string
     */
    public function getData()
    {
        $raw = file_get_contents($this->urn);
        if (false === $raw) {
            throw new \RuntimeException(sprintf('File %s not found', $this->urn));
        }
        return $raw;
    }

}