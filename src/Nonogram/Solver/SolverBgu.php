<?php

namespace Nonogram\Solver;

/**
 * Class SolverBgu
 *
 * Wrapper for external solver "Bgu Solver"
 *
 * @package Nonogram\Solver
 */
class SolverBgu extends AbstractSolverExternal implements AnySolver
{

    /**
     * SolverBgu constructor.
     * @param \Nonogram\View\ViewNin $viewNin
     * @param \Nonogram\LevelParser\LevelParserDat $levelParserDat
     */
    public function __construct(
        \Nonogram\View\ViewNin $viewNin,
        \Nonogram\LevelParser\LevelParserDat $levelParserDat
    )
    {
        parent::__construct($viewNin, $levelParserDat);
    }

    /**
     * @param $output
     * @return \Nonogram\Cell\AnyCell[][]
     */
    protected function parseOutput($output)
    {
        $this->levelParserDat->setRawData(strstr($output,"\n\n"));
        $this->levelParserDat->overrideChars(' ', '#');
        $cells = $this->levelParserDat->getGrid();

        return $cells;
    }

    /**
     * @param $output
     */
    protected function extractSolvingStatistics($output)
    {
        $solvingStatsRaw = trim(strstr($output,"\n\n",true));
        foreach(preg_split("/(\r\n|\n|\r)/", $solvingStatsRaw) as $statLine) {
            list($key, $val) = explode(': ', $statLine);
            $this->solvingStatistics[$key] = $val;
        }
    }

    /**
     * @param $output
     */
    protected function getSolverOutput()
    {
        $tempFile = tempnam(null, null);
        $cmd = 'java -jar ' . $this->settingExecutablePath() . ' -file ' . $tempFile;
        file_put_contents($tempFile, $this->view->drawField());
        $output = shell_exec($cmd);
        unlink($tempFile);

        return $output;
    }

}