<?php

namespace Nonogram\Controller;
use Nonogram\View\ViewWritableInterface;

/**
 * @package Nonogram\Controller
 */
abstract class AbstractSubController implements AnyController
{

    /**
     * @var \Nonogram\Grid\Factory
     */
    protected $gridFactory;

    /**
     * @var \Nonogram\View\ViewInterface
     */
    protected $view;

    /**
     * @var array
     */
    protected $inputParameter;

    /**
     * @var string
     */
    protected $solvingStatistics;

    /**
     * @var string
     */
    protected $solvingStatisticsTotal;

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    private $fileFinder;

    /**
     * @var string
     */
    const FILENAME_WILDCARD_REGEX = '~\[([0-9]+)-([0-9]+)\]~';

    /**
     * AbstractSubController constructor.
     * @param \Nonogram\Grid\Factory $gridFactory
     * @param \Nonogram\View\ViewInterface $view
     */
    public function __construct(
        \Nonogram\Grid\Factory $gridFactory,
        \Nonogram\View\ViewInterface $view,
        \Symfony\Component\Finder\Finder $finder
    )
    {
        $this->gridFactory = $gridFactory;
        $this->view = $view;
        $this->fileFinder = $finder;
    }

    /**
     * @param array $input
     */
    public function setInputParameter(array $input)
    {
        $this->inputParameter = $input;
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $this->inputParameter['level_filename'] = (array)$this->inputParameter['level_filename'];
        foreach($this->inputParameter['level_filename'] as $key => $urn) {

            if (is_dir($urn)) {
                $this->fileFinder->files()->in($urn)->name('*.yml');
                foreach ($this->fileFinder as $file) {
                    $this->inputParameter['level_filename'][] = $file->getRealpath();
                }
                unset($this->inputParameter['level_filename'][$key]);
            }
            elseif(1 === preg_match(self::FILENAME_WILDCARD_REGEX, $urn, $matches)) {
                $lowerBorder = (int)$matches[1];
                $upperBorder = (int)$matches[2];
                foreach(range($lowerBorder, $upperBorder) as $number) {
                    $this->inputParameter['level_filename'][] = preg_replace(self::FILENAME_WILDCARD_REGEX, $number, $urn);
                }
                unset($this->inputParameter['level_filename'][$key]);
            }

        }

        foreach($this->inputParameter['level_filename'] as $urn) {
            try {
                $grid = $this->gridFactory->get($urn);
                $solvingStatistics = $grid->getSolvingStatistics();
                if(null !== $solvingStatistics) {
                    $this->addSolvingStatistics($solvingStatistics);
                }
                $this->view->setGrid($grid);
                $this->view->setTitle(basename($urn));
                if(!($this->view instanceof ViewWritableInterface && $this->view->supportsMultiple())) {
                    //execute after adding one
                    $this->executeAction($urn);
                }
            }
            catch(\Exception $e) {
                echo $e->getMessage() . ': ' . $urn . PHP_EOL;
                continue;
            }
        }
        if($this->view instanceof ViewWritableInterface && $this->view->supportsMultiple()) {
            //execute after adding multiple
            $this->executeAction($urn);
        }
        if(method_exists($this, 'finallyExecute')) {
            $this->finallyExecute();
        }
    }

    /**
     * @param string $urn
     */
    abstract protected function executeAction($urn);

    /**
     * @param array $solvingStatistics
     */
    private function addSolvingStatistics(array $solvingStatistics)
    {
        $this->solvingStatistics = $solvingStatistics;
        foreach($solvingStatistics as $key => $value) {
            if(!isset($this->solvingStatisticsTotal[$key])) {
                $this->solvingStatisticsTotal[$key] = $value;
            }
            else {
                $this->solvingStatisticsTotal[$key] += $value;
            }
        }
    }

}
