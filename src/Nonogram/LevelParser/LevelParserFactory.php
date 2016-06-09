<?php

namespace Nonogram\LevelParser;

class LevelParserFactory
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
        foreach($this->parsers as $parser) {
            if($parser->canHandle($urn)) {
                return $parser;
            }
        }
        throw new \RuntimeException('no suitable parser found for file '.$urn);
    }

}