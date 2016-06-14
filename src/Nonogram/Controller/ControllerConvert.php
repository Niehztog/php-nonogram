<?php

namespace Nonogram\Controller;

/**
 * @package Nonogram\Controller
 */
class ControllerConvert extends AbstractSubController implements AnyController
{

    /**
     * @var GridSaverFile
     */
    private $gridSaver;

    /**
     * @var string
     */
    private $defaultDir;

    /**
     * ControllerConvert constructor.
     * @param \Nonogram\Grid\Factory $gridFactory
     * @param \Nonogram\View\ViewWritableInterface $view
     * @param \Symfony\Component\Finder\Finder $finder
     * @param \Nonogram\Grid\Saver\GridSaverFile $gridSaver
     */
    public function __construct(
        \Nonogram\Grid\Factory $gridFactory,
        \Nonogram\View\ViewWritableInterface $view,
        \Symfony\Component\Finder\Finder $finder,
        \Nonogram\Grid\Saver\GridSaverFile $gridSaver
    ) {
        $this->gridSaver = $gridSaver;
        $this->gridSaver->setView($view);
        parent::__construct($gridFactory, $view, $finder);
    }

    /**
     * @param string $urn
     */
    protected function executeAction($urn)
    {
        $filenameSave = basename($urn);
        if($filenameSave) {
            $filenameSave = substr_replace($filenameSave, $this->view->getFileExtension(), strrpos($urn, '.') + 1);
        }
        else {
            $id = $this->view->getGrid()->getId();
            if(empty($id)) {
                throw new \RuntimeException('Puzzle needs to provide an id value in order to determine filename for writing.');
            }
            $filenameSave = $id . '.' . $this->view->getFileExtension();
        }

        if($this->defaultDir && file_exists($this->defaultDir) && is_writable($this->defaultDir)) {
            $dir = $this->defaultDir;
        }
        else {
            $dir = dirname($urn);
            if(!$dir || !file_exists($dir) || !is_writable($dir)) {
                throw new \RuntimeException('Either provide a default directory for saving puzzles or make sure the path \''.$dir.'\' is writeable.');
            }
        }

        $fullPathSave = $dir . DIRECTORY_SEPARATOR . $filenameSave;

        $this->gridSaver->save($fullPathSave);

        echo 'File written: ' . $fullPathSave . PHP_EOL;
    }

    /**
     * @param string $defaultDir
     */
    public function setDefaultDirectory($defaultDir)
    {
        $this->defaultDir = $defaultDir;
    }

}
