<?php

namespace Nonogram\LevelParser;

class LevelParserXml extends AbstractLevelParser implements LevelParserInterface, LevelParserMetaDataInterface
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
     * @param $rawData
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function setRawData($rawData)
    {
        if (1 === preg_match('~Puzzle [0-9]+ does not exist~', $rawData)) {
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
        return  parent::canHandle($urn) || 1 === preg_match('~http:\/\/webpbn\.com\/XMLpuz\.cgi\?id=[0-9]+~', $urn);
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
