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

}
