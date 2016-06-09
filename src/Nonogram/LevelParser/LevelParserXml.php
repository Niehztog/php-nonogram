<?php

namespace Nonogram\LevelParser;

class LevelParserXml extends AbstractLevelParser implements LevelParserLabelInterface
{

    /**
     * @var \Nonogram\Label\Factory
     */
    private $labelFactory;

    /**
     * LevelParserYaml constructor.
     * @param \Nonogram\Label\Factory $labelFactory
     */
    public function __construct(\Nonogram\Label\Factory $labelFactory)
    {
        $this->labelFactory = $labelFactory;
    }

    /**
     * @param string $urn
     * @return bool
     */
    public function canHandle($urn)
    {
        return  'xml' === $this->getExtension($urn) || 1 === preg_match('~http:\/\/webpbn\.com\/XMLpuz\.cgi\?id=[0-9]+~', $urn);
    }

    /**
     * @return Label
     */
    public function getLabels()
    {
        if(1 === preg_match('~Puzzle [0-9]+ does not exist~', $this->rawData)) {
            throw new \InvalidArgumentException(trim($this->rawData));
        }

        $array = array();

        $doc = new \DOMDocument();
        $doc->preserveWhiteSpace = false;
        if(false === $doc->loadXML($this->rawData, LIBXML_NOERROR)) {
            throw new \RuntimeException('failed to parse xml');
        }
        $xpath = new \DOMXPath($doc);
        foreach(array('columns', 'rows') as $direction) {
            $array[$direction] = array();
            $query = '//puzzleset/puzzle/clues[@type="'.$direction.'"]/line';
            $entries = $xpath->query($query);
            foreach ($entries as $entry) {
                $sequence = array();
                $lines = $entry->getElementsByTagName('count');
                foreach ($lines as $line) {
                    $sequence[] = $line->nodeValue;
                }
                $array[$direction][] = $sequence;
            }
        }
        
        return $this->labelFactory->getFromRaw($array);
    }

}
