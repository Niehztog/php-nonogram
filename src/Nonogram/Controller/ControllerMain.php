<?php

namespace Nonogram\Controller;

use Nonogram\Grid\Saver\GridSaverFile;

/**
 * Class ControllerMain
 * @package Nonogram\Application
 */
class ControllerMain implements AnyController
{

    /**
     * @var \Nonogram\Controller\ControllerGame
     */
    private $subController;

    /**
     * ControllerMain constructor.
     * @param AbstractSubController $subController
     */
    public function __construct(
        \Nonogram\Controller\AbstractSubController $subController
    ) {
        $this->subController = $subController;
    }

    /**
     *
     */
    public function run()
    {
        $this->subController->run();
    }

    /**
     * @param array $input
     */
    public function setInputParameter(array $input)
    {
        $this->subController->setInputParameter($input);
    }

}
