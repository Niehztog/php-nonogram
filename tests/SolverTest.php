<?php

class SolverTest extends AbstractTest
{
    /**
     * Tests that the solver doesn't come to false conclusions
     *
     * Verifies only that all cells marked by the solver correspond to the actual solution
     * Does *not* check that the solver solved all cells (unknown cells are skipped)
     * @test
     */
    public function testSolverAllDataProvider()
    {
        $rootDir = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;
        $filePathFull = $rootDir . 'data' . DIRECTORY_SEPARATOR . 'Levels' . DIRECTORY_SEPARATOR;

        $finder = new \Symfony\Component\Finder\Finder();
        $finder->files()->in($filePathFull)->name('*.dat');

        $levelList = array();
        foreach ($finder as $file) {
            $levelFullPath = $file->getRealpath();
            $levelList[] = array(substr_replace($levelFullPath, '', strrpos($levelFullPath, '.')));
        }

        return $levelList;
    }

    /**
     * Tests that the solver doesn't come to false conclusions
     *
     * Verifies only that all cells marked by the solver correspond to the actual solution
     * Does *not* check that the solver solved all cells (unknown cells are skipped)
     * @test
     * @param $filepath
     *
     * @dataProvider testSolverAllDataProvider
     */
    public function testSolverAll($filename)
    {
        $grid = $this->loadFile($filename . '.dat');
        $cellsExpected = $grid->getCells();
        $labels = $grid->getLabels();
        $labelsRaw['columns'] = $labels->getCol();
        $labelsRaw['rows'] = $labels->getRow();
        $labelFactory = new \Nonogram\Label\Factory(new \Nonogram\Label\LabelProviderCells());
        $labelFactory->setContainer($this->container);
        $parserYaml = new \Nonogram\LevelParser\LevelParserYaml(
            $labelFactory,
            new \Symfony\Component\Yaml\Parser()
        );
        $solver = $this->container->get('solver');
        $yamlDumper = new \Symfony\Component\Yaml\Dumper();
        $parserYaml->setRawData($yamlDumper->dump($labelsRaw));
        $labelsLoaded = $parserYaml->getLabels();
        $cellsActual = $solver->solve($labelsLoaded);

        $this->assertGridsEqual($cellsExpected, $cellsActual, $filename);
    }

    /**
     * Loads a grid layout from file
     *
     * @param $filePathFull
     * @return \Nonogram\Grid\Grid
     */
    private function loadFile($filePathFull)
    {
        $factory = $this->container->get('grid_factory');
        $grid = $factory->get($filePathFull);
        return $grid;
    }
}
