<?php

namespace Nonogram\View;

/**
 * Interface ViewWritableInterface
 * @package Nonogram\View
 */
interface ViewWritableInterface
{

    /**
     * In case output format supports being written to a file, this method returns the suitable file extension
     * @return string
     */
    public function getFileExtension();

    /**
     * Indicates whether View supports storing multiple puzzles in one instance or just one
     * @return boolean
     */
    public function supportsMultiple();

}