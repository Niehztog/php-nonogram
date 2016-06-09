<?php

namespace Nonogram\LevelParser;

class LevelParserYaml extends AbstractLevelParser implements LevelParserLabelInterface
{

    /**
     * @var \Nonogram\Label\Factory
     */
    private $labelFactory;

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
        $this->labelFactory = $labelFactory;
        $this->yamlParser = $yamlParser;
    }

    /**
     * @param string $urn
     * @return bool
     */
    public function canHandle($urn)
    {
        return 'yml' === $this->getExtension($urn);
    }

    /**
     * @return Label
     */
    public function getLabels()
    {
        $array = $this->yamlParser->parse($this->rawData);
        if(!isset($array['columns']) || !isset($array['rows'])) {
            throw new \RuntimeException('file in unexpected format');
        }

        return $this->labelFactory->getFromRaw($array);
    }

}
