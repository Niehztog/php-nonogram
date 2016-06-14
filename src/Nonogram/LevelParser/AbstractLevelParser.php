<?php

namespace Nonogram\LevelParser;

abstract class AbstractLevelParser implements LevelParserInterface
{
    /**
     * @var string
     */
    protected $rawData;

    /**
     * @var \Nonogram\Label\Factory
     */
    protected $labelFactory;

    /**
     * LevelParserYaml constructor.
     * @param \Nonogram\Label\Factory $labelFactory
     * @param \Symfony\Component\Yaml\Parser $yamlParser
     */
    public function __construct(\Nonogram\Label\Factory $labelFactory)
    {
        $this->labelFactory = $labelFactory;
    }

    /**
     * @param $rawData
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * @param string $urn
     * @return bool
     */
    public function canHandle($urn)
    {
        return $this->getFileExtension() === $this->extractExtension($urn);
    }

    /**
     * Returns the grid information - if not available
     * should be generated out of the grid information
     *
     * @return Label
     */
    abstract public function getLabels();

    /**
     * Returns the grid information - if available
     *
     * @return \Nonogram\Cell\AnyCell[][]
     */
    public function getGrid()
    {
        return array();
    }

    /**
     * Internal helper function - extracts file extension
     * from a filename string
     *
     * @return mixed
     */
    protected function extractExtension($urn)
    {
        return pathinfo($urn, PATHINFO_EXTENSION);
    }

}
