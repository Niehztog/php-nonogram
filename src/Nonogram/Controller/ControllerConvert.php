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
        //$result = preg_match('~https:\/\/raw\.githubusercontent\.com\/Substance12\/Picroxx\/master\/game\/courses\/([a-z]+)\/([a-z]+)\/([a-z0-9]+)\/([a-z]+)\.lua~', $urn, $matches);
        //$filenameSave = 'C:\\Users\\Nils\\Documents\\php-nonogram\\data\\Levels\\mariopicross\\' . $matches[2] . '-'. $matches[3] . '-'. $matches[4] . '.' . $this->view->getFileExtension();

        $filenameSave = substr_replace($urn, $this->view->getFileExtension(), strrpos($urn, '.') +1);
        $this->gridSaver->save($filenameSave);

        echo 'File written: ' . $filenameSave . PHP_EOL;
    }
}
