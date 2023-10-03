<?php

namespace Nonogram\Solver;

/**
 * Class Solver
 *
 * basically a collection of solving strategies
 *
 * @package Nonogram\Solver
 */
abstract class AbstractSolverExternal extends AbstractSolver implements AnySolver
{

    /**
     * @var \Nonogram\View\ViewXml
     */
    protected $view;

    /**
     * @var \Nonogram\LevelParser\LevelParserDat
     */
    protected $levelParserDat;

    /**
     * Path to executable of pbnsolve
     * @var string
     */
    private $settingExecutablePath;

    /**
     * AbstractSolverExternal constructor.
     * @param \Nonogram\View\AbstractView $view
     * @param \Nonogram\LevelParser\LevelParserDat $levelParserDat
     */
    public function __construct(
        \Nonogram\View\AbstractView $view,
        \Nonogram\LevelParser\LevelParserDat $levelParserDat
    )
    {
        $this->view = $view;
        $this->levelParserDat = $levelParserDat;
    }

    /**
     * @param \Nonogram\Label\Label $labels
     * @return array
     */
    public function solve(\Nonogram\Grid\Grid $grid)
    {
        $this->view->setGrid($grid);
        $output = $this->getSolverOutput();
        $this->extractSolvingStatistics($output);

        $cells = $this->parseOutput($output);
        $grid->setCells($cells);
        return true;
    }

    /**
     * @param $pipes
     * @return string
     */
    abstract protected function getSolverOutput();

    /**
     * @param $output
     */
    abstract protected function extractSolvingStatistics($output);

    /**
     * @param $output
     * @return \Nonogram\Cell\AnyCell[][]
     */
    abstract protected function parseOutput($output);

    /**
     * Sets/gets path to pbnsolve executable file
     *
     * @param null $newVal
     * @return bool
     */
    public function settingExecutablePath($newVal = null)
    {
        if(is_string($newVal)) {
            $this->settingExecutablePath = $newVal;
        }
        else {
            return $this->settingExecutablePath;
        }
    }

}