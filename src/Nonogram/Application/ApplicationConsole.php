<?php

namespace Nonogram\Application;

use Nonogram\Grid\Grid;

/**
 * Class ApplicationConsole
 * @package Nonogram\Application
 */
class ApplicationConsole
{

    private $provideInteraction;

    /**
     *
     */
    public function run()
    {
        $rootDir = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;

        //$fileName = 'mp_level1a.dat';
        $fileName = 'alona1.yml';
        $filePathFull = $rootDir . 'data' . DIRECTORY_SEPARATOR . 'Levels' . DIRECTORY_SEPARATOR . $fileName;

        $grid = $this->loadFile($filePathFull);
        $this->renderGrid($grid);
    }

    /**
     * Loads a grid layout from file
     *
     * @param $filePathFull
     */
    private function loadFile($filePathFull)
    {
        $factory = new \Nonogram\Grid\Factory($filePathFull);
        $grid = $factory->getFromFile($filePathFull);
        $this->provideInteraction = $factory::SOURCE_GRID === $factory->getLoadedSource();
        return $grid;
    }

    /**
     * @param Grid $grid
     */
    private function renderGrid(Grid $grid)
    {
        $factory = new \Nonogram\Renderer\Factory($grid);
        $factory->setInteractive($this->provideInteraction);
        $renderer = $factory->get();
        $renderer->render();
    }
}
