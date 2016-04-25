<?php

namespace Nonogram\Application;

use Nonogram\Grid\Grid;
use Nonogram\Grid\Saver\GridSaverFile;

/**
 * Class ApplicationConsole
 * @package Nonogram\Application
 */
class ApplicationConsole
{

    /**
     * @var bool
     */
    private $provideInteraction;

    /**
     * @var string
     */
    private $levelFilename;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var \Nonogram\Grid\Factory
     */
    private $gridFactory;

    /**
     * @var GridSaverFile
     */
    private $gridSaver;

    /**
     * @var \Nonogram\Renderer\Factory
     */
    private $rendererFactory;

    /**
     * ApplicationConsole constructor.
     * @param \Nonogram\Grid\Factory $gridFactory
     * @param \Nonogram\Renderer\Factory $rendererFactory
     */
    public function __construct(\Nonogram\Grid\Factory $gridFactory, \Nonogram\Renderer\Factory $rendererFactory, \Nonogram\Grid\Saver\GridSaverFile $gridSaver)
    {
        $this->gridFactory = $gridFactory;
        $this->rendererFactory = $rendererFactory;
        $this->gridSaver = $gridSaver;
    }

    public function setLevelFilename($filename)
    {
        $this->levelFilename = $filename;
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     *
     */
    public function run()
    {
        $rootDir = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;
        $filePathFull = $rootDir . 'data' . DIRECTORY_SEPARATOR . 'Levels' . DIRECTORY_SEPARATOR . $this->levelFilename;

        $grid = $this->loadFile($filePathFull);
        if('convert' === $this->mode) {
            $this->gridSaver->setGrid($grid);
            $this->gridSaver->setFormat($this->gridFactory->isLoadedFromGrid() ? GridSaverFile::FILE_FORMAT_YAML : GridSaverFile::FILE_FORMAT_DAT);
            $filenameSave = substr_replace($filePathFull , ($this->gridFactory->isLoadedFromGrid() ? 'yml' : 'dat'), strrpos($filePathFull , '.') +1 );
            $this->gridSaver->save($filenameSave);
        }
        else {
            $this->renderGrid($grid);
        }
    }

    /**
     * Loads a grid layout from file
     *
     * @param $filePathFull
     */
    private function loadFile($filePathFull)
    {
        $grid = $this->gridFactory->getFromFile($filePathFull);
        switch($this->mode) {
            case 'interactive':
                $this->provideInteraction = true;
                break;
            case 'display':
                $this->provideInteraction = false;
                break;
            case 'auto':
            default:
                $this->provideInteraction = $this->gridFactory->isLoadedFromGrid();
                break;
        }

        return $grid;
    }

    /**
     * @param Grid $grid
     */
    private function renderGrid(Grid $grid)
    {
        $this->rendererFactory->setInteractive($this->provideInteraction);
        $renderer = $this->rendererFactory->get($grid);
        $renderer->render();
    }
}
