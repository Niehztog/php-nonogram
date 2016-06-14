<?php

namespace Nonogram\LevelParser;

class LevelParserYaml extends AbstractLevelParser implements LevelParserInterface
{
    /**
     * @var \Symfony\Component\Yaml\Parser
     */
    private $yamlParser;

    /**
     * LevelParserYaml constructor.
     * @param \Nonogram\Label\Factory $labelFactory
     * @param \Symfony\Component\Yaml\Parser $yamlParser
     */
    public function __construct(\Nonogram\Label\Factory $labelFactory, \Symfony\Component\Yaml\Parser $yamlParser)
    {
        $this->yamlParser = $yamlParser;
        parent::__construct($labelFactory);
    }

    /**
     * @return Label
     */
    public function getLabels()
    {
        $array = $this->yamlParser->parse($this->rawData);
        if (!isset($array['columns']) || !isset($array['rows'])) {
            throw new \RuntimeException('file in unexpected format');
        }

        return $this->labelFactory->getFromRaw($array);
    }

    /**
     * This method returns the supported file extension
     * @return string
     */
    public function getFileExtension()
    {
        return 'yml';
    }
    
}
