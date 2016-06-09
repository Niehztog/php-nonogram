<?php

namespace Nonogram\LevelDataSource;

interface LevelDataSourceInterface
{

    /**
     * @param string $urn
     */
    public function setUrn($urn);

    /**
     * @param string $urn
     */
    //public function getUrn($urn);

    /**
     * @param string $urn
     * @return bool
     */
    public function canHandle($urn);

    /**
     * @return string
     */
    public function getData();

}