<?php

namespace Nonogram\LevelParser;

interface LevelParserInterface
{
    /**
     * @param $rawData
     */
    public function setRawData($rawData);

    /**
     * @param string $urn
     * @return bool
     */
    public function canHandle($urn);

    /**
     * Returns the grid information - if not available
     * should be generated out of the grid information
     *
     * @return Label
     */
    public function getLabels();

    /**
     * Returns the grid information - if available
     *
     * @return \Nonogram\Cell\AnyCell[][]
     */
    public function getGrid();

    /**
     * This method returns the supported file extension
     * @return string
     */
    public function getFileExtension();

}
