<?php

namespace Nonogram\LevelParser;

class LevelParserXml extends AbstractLevelParserGrid implements LevelParserInterface, LevelParserMetaDataInterface
{

    /**
     * @var \DOMDocument
     */
    private $domDocument;

    /**
     * @var \DOMXPath
     */
    private $domXPath;

    /**
     * @var \Nonogram\LevelParser\LevelParserDat
     */
    private $levelParserDat;

    /**
     * LevelParserXml constructor.
     * @param \Nonogram\Label\Factory $labelFactory
     * @param \Nonogram\Cell\Factory $cellFactory
     * @param LevelParserDat $levelParserDat
     */
    public function __construct(
        \Nonogram\Label\Factory $labelFactory,
        \Nonogram\Cell\Factory $cellFactory,
        \Nonogram\LevelParser\LevelParserDat $levelParserDat
    ) {
        $this->levelParserDat = $levelParserDat;
        parent::__construct($labelFactory, $cellFactory);
    }

    /**
     * @param $rawData
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function setRawData($rawData)
    {
        if (1 === preg_match('~Puzzle [0-9]+ (?:does not exist|has not been published)~', $rawData)) {
            throw new \InvalidArgumentException(trim($rawData));
        }

        parent::setRawData($rawData);

        $this->domDocument = new \DOMDocument();
        $this->domDocument->preserveWhiteSpace = false;
        if (false === $this->domDocument->loadXML($this->rawData, LIBXML_NOERROR)) {
            throw new \RuntimeException('failed to parse xml');
        }
        $this->domXPath = new \DOMXPath($this->domDocument);
    }

    /**
     * @param string $urn
     * @return bool
     */
    public function canHandle($urn)
    {
        return  parent::canHandle($urn) || 1 === preg_match('~http:\/\/webpbn\.com\/(?:XMLpuz|export)\.cgi\?id=[0-9]+~', $urn);
    }

    /**
     * @return \Nonogram\Label\Label
     */
    public function getLabels()
    {
        $labelsRaw = [];

        foreach (array('columns', 'rows') as $direction) {
            $labelsRaw[$direction] = [];
            $query = '//puzzleset/puzzle/clues[@type="'.$direction.'"]/line';
            $domNodeList = $this->domXPath->query($query);
            foreach ($domNodeList as $entry) {
                $sequence = [];
                $lines = $entry->getElementsByTagName('count');
                foreach ($lines as $line) {
                    if ($line->hasAttribute('color')) {
                        throw new \RuntimeException('puzzle relies on colors - not supported');
                    }
                    $sequence[] = $line->nodeValue;
                }
                $labelsRaw[$direction][] = $sequence;
            }
        }
        
        return $this->labelFactory->getFromRaw($labelsRaw);
    }

    /**
     * Returns the grid information - if available
     * must be overridden by child implementation
     *
     * @return \Nonogram\Cell\AnyCell[][]
     */
    public function getGrid()
    {
        $layout = $this->retrieveNodeValue('solution[@type="goal"]/image');
        if(empty($layout)) {
            return array();
        }
        $layout = str_replace('|','',trim($layout));

        return $this->parseLayout($layout);
    }

    /**
     * Some formats MAY provide grids, those can override this method to determine
     * its ability to provide grids dynamically
     * @return bool
     */
    public function hasGrid()
    {
        $layout = $this->retrieveNodeValue('solution[@type="goal"]/image');
        return !empty($layout);
    }

    /**
     * @param $output
     * @return \Nonogram\Cell\AnyCell[][]
     */
    private function parseLayout($output)
    {
        $this->levelParserDat->setRawData($output);
        $this->levelParserDat->useColors();
        $cells = $this->levelParserDat->getGrid();

        return $cells;
    }

    /**
     * Returns the char representing an empty cell in the grid
     * @return string
     */
    protected function getCharEmpty() {}

    /**
     * @return string
     */
    public function getId()
    {
        $idField = $this->retrieveNodeValue('id');
        if(empty($idField)) {
            return 0;
        }
        preg_match('~#([0-9]+)(?: \(v\.[0-9]+\))?~', $idField, $matches);

        return (int)$matches[1];
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->retrieveNodeValue('title');
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->retrieveNodeValue('author');
    }

    /**
     * @return string
     */
    public function getCopyright()
    {
        return $this->retrieveNodeValue('copyright');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->retrieveNodeValue('description');
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return ''; //not supported
    }

    /**
     * @param string $nodeName
     * @return string
     */
    private function retrieveNodeValue($nodeName)
    {
        $domNodeList = $this->domXPath->query('//puzzleset/puzzle/'.$nodeName.'[1]');
        $node = $domNodeList->item(0);
        if(null === $node) {
            return '';
        }
        return $node->nodeValue;
    }

    /**
     * This method returns the supported file extension
     * @return string
     */
    public function getFileExtension()
    {
        return 'xml';
    }

}
