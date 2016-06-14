<?php

namespace Nonogram\LevelParser;

interface LevelParserMetaDataInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getAuthor();

    /**
     * @return string
     */
    public function getCopyright();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getCreated();

}