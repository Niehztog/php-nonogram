<?php

namespace Nonogram\LevelParser;

class Factory
{
    /**
     * @var LevelParserInterface[]
     */
    private $parsers;

    /**
     * @param LevelParserInterface $parser
     */
    public function attachParser(LevelParserInterface $parser)
    {
        $this->parsers[] = $parser;
    }

    /**
     * @return LevelParserInterface
     */
    public function get($urn)
    {
        foreach ($this->parsers as $parser) {
            if ($parser->canHandle($urn)) {
                return $parser;
            }
        }
        throw new \RuntimeException('no suitable parser found for file '.$urn);
    }

    /**
     * Returns list of all supported file extensions
     * @return array
     */
    public function getFileExtensions()
    {
        $list = [];
        foreach ($this->parsers as $parser) {
            $list[] = $parser->getFileExtension();
        }
        return $list;
    }

}
