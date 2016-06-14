<?php

namespace Nonogram\Controller;

/**
 * @package Nonogram\Controller
 */
class ControllerDisplay extends AbstractSubController implements AnyController
{

    /**
     * ControllerDisplay constructor.
     * @param \Nonogram\Grid\Factory $gridFactory
     * @param \Nonogram\View\ViewInterface $view
     * @param \Symfony\Component\Finder\Finder $finder
     */
    public function __construct(
        \Nonogram\Grid\Factory $gridFactory,
        \Nonogram\View\ViewInterface $view,
        \Symfony\Component\Finder\Finder $finder
    )
    {
        parent::__construct($gridFactory, $view, $finder);
    }

    /**
     * @param string $urn
     */
    protected function executeAction($urn)
    {
        echo PHP_EOL . $urn . PHP_EOL;
        echo $this->view->drawField();
        
        $solvingStatistics = $this->view->getGrid()->getSolvingStatistics();
        if(null !== $solvingStatistics) {
            $this->addSolvingStatistics($solvingStatistics);
        }

        if (!empty($this->solvingStatistics)) {
            $this->displaySolvingStatistics($this->solvingStatistics);
        }
    }

    protected function finallyExecute()
    {
        if(count($this->inputParameter['level_filename']) > 1 && !empty($this->solvingStatisticsTotal)) {
            echo '==========================================' . PHP_EOL;
            $this->displaySolvingStatistics($this->solvingStatisticsTotal);
        }
    }

    /**
     * @param $solvingStatistics
     */
    private function displaySolvingStatistics($solvingStatistics)
    {
        echo 'Solving statistics:' . PHP_EOL;
        foreach ($solvingStatistics as $ruleClass => $ruleCounter) {
            echo $ruleClass . ': ' . $ruleCounter.PHP_EOL;
        }
    }
}
