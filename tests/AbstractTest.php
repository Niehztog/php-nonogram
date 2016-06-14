<?php

use Nonogram\Cell\AnyCell;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{

    protected $container;

    /**
     * @var \Nonogram\Solver\RunRange\RunRangeFactory
     */
    protected $runRangeFactory;

    /**
     * @var \Nonogram\Cell\Factory
     */
    protected $cellFactory;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->initContainer();
        $this->cellFactory = new \Nonogram\Cell\Factory();
        $this->cellFactory->setContainer($this->container);
        $this->runRangeFactory = new \Nonogram\Solver\RunRange\RunRangeFactory();
        $this->runRangeFactory->setContainer($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @return \Nonogram\Solver\SolverJ54
     */
    private function initContainer()
    {
        $this->container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
        $loader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($this->container, new \Symfony\Component\Config\FileLocator(realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Nonogram'.DIRECTORY_SEPARATOR.'Config')));
        $loader->load('container.yml');
        $this->container->compile();
    }

    protected function convertRowRawToActual($rowString)
    {
        $field = array();
        for ($i = 0; $i < strlen($rowString); $i++) {
            switch ($rowString{$i}) {
                case 'U':
                    $cell = $this->cellFactory->getUnknown();
                    break;
                case 'B':
                    $cell = $this->cellFactory->getBox();
                    break;
                case 'E':
                    $cell = $this->cellFactory->getEmpty();
                    break;
            }
            $field[] = $cell;
        }

        return $field;
    }

    protected function assertGridsEqual(array $cellsExpected, array $cellsActual, $filename = '')
    {
        foreach ($cellsExpected as $i => $row) {
            foreach ($row as $j => $cell) {
                /*if ($cellsActual[$i][$j]->getType() === AnyCell::TYPE_UNKNOWN) {
                    continue;
                }*/
                $this->assertEquals($cellsExpected[$i][$j]->getType(), $cellsActual[$i][$j]->getType(), $filename);
            }
        }
    }

}
