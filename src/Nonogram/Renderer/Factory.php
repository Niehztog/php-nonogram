<?php

namespace Nonogram\Renderer;

/**
 * Class Factory
 * @package Nonogram\Renderer
 */
class Factory
{
    /**
     * Supported environments - console application
     * @var int
     */
    const ENVIRONMENT_CONSOLE = 1;

    /**
     * @var \Nonogram\Grid\Grid
     */
    private $grid;

    /**
     * Provide version which allows user interactions or merely display grid
     * @var bool
     */
    private $interactive = true;

    /**
     * Factory constructor.
     * @param \Nonogram\Grid\Grid $grid
     */
    public function __construct(\Nonogram\Grid\Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @param boolean $interactive
     */
    public function setInteractive(bool $interactive)
    {
        $this->interactive = $interactive;
    }

    /**
     * @return FieldRendererConsole|null
     */
    public function get()
    {
        return $this->getForEnvironment(self::ENVIRONMENT_CONSOLE);
    }

    /**
     * @param $environment
     * @return FieldRendererConsole|null
     */
    private function getForEnvironment($environment)
    {
        $instance = null;
        switch ($environment) {
            default:
            case self::ENVIRONMENT_CONSOLE:
                $instance = new FieldRendererConsole($this->grid);
                break;
        }
        $instance->setInteractive($this->interactive);
        return $instance;
    }
}
