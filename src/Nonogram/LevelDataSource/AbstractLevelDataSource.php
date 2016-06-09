<?php

namespace Nonogram\LevelDataSource;

abstract class AbstractLevelDataSource implements LevelDataSourceInterface
{

    /**
     * @var string
     */
    protected $urn;

    /**
     * @param string $urn
     */
    public function setUrn($urn)
    {
        $this->urn = $urn;
    }

}
