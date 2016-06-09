<?php

namespace Nonogram\Controller;

/**
 * Interface AnyController
 * @package Nonogram\Controller
 */
interface AnyController
{
    /**
     * @return mixed
     */
    public function run();

    /**
     * @param array $input
     */
    public function setInputParameter(array $input);

}
