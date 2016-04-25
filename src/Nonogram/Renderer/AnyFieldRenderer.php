<?php

namespace Nonogram\Renderer;

/**
 * Interface AnyFieldRenderer
 * @package Nonogram\Renderer
 */
interface AnyFieldRenderer
{
    /**
     * Provide version which allows user interactions or merely display grid
     * @param $interactive
     * @return mixed
     */
    public function setInteractive($interactive);

    /**
     * @return mixed
     */
    public function render();
}
