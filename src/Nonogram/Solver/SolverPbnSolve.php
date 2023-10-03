<?php

namespace Nonogram\Solver;

/**
 * Class SolverPbnSolve
 *
 * Wrapper for external solver "PhnSolve"
 *
 * @package Nonogram\Solver
 */
class SolverPbnSolve extends AbstractSolverExternal implements AnySolver
{

    /**
     * SolverPbnSolve constructor.
     * @param \Nonogram\View\ViewXml $viewXml
     * @param \Nonogram\LevelParser\LevelParserDat $levelParserDat
     */
    public function __construct(
        \Nonogram\View\ViewXml $viewXml,
        \Nonogram\LevelParser\LevelParserDat $levelParserDat
    )
    {
        parent::__construct($viewXml, $levelParserDat);
    }

    /**
     * @param $output
     * @return \Nonogram\Cell\AnyCell[][]
     */
    protected function parseOutput($output)
    {
        $this->levelParserDat->setRawData($output);
        $this->levelParserDat->useColors();
        $cells = $this->levelParserDat->getGrid();

        return $cells;
    }

    /**
     * @param $output
     */
    protected function extractSolvingStatistics($output)
    {
        $solvingStatsRaw = trim(strstr($output, 'Cells Solved:'));
        foreach(preg_split("/(\r\n|\n|\r)/", $solvingStatsRaw) as $statLine) {
            list($key, $val) = explode(': ', $statLine);
            $this->solvingStatistics[$key] = $val;
        }
    }

    /**
     * @param $pipes
     * @return string
     */
    protected function getSolverOutput()
    {
        $cmd = $this->settingExecutablePath() . ' -t';

        $settingOld = $this->view->settingIncludeSolution();
        $this->view->settingIncludeSolution(false);
        $stdIn = $this->view->drawField();
        $this->view->settingIncludeSolution($settingOld);

        $descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w")
        );

        $process = proc_open($cmd, $descriptorspec, $pipes);
        if (!is_resource($process)) {
            throw new \RuntimeException('failed to execute cmd solver');
        }

        fwrite($pipes[0], $stdIn);
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        proc_close($process);

        if (0 === preg_match('~((STOPPED WITH)|(UNIQUE( LINE)?)) SOLUTION\:~', $output)) {
            throw new \Nonogram\Solver\Exception\NoUniqueSolutionException('puzzle has no unique solution');
        }
        return $output;
    }

}