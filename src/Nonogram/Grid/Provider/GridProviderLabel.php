<?php

namespace Nonogram\Grid\Provider;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class GridProviderLabel implements AnyGridProvider
{
    private $data;

    private $solvingStatistics;

    /**
     * @param \Nonogram\Label\Label $labels
     */
    public function setLabels(\Nonogram\Label\Label $labels)
    {
        $solver = $this->getSolver();
        $this->data = $solver->solve($labels);
        $this->solvingStatistics = $solver->getSolvingStatistics();
    }

    /**
     * @return array
     */
    public function provide()
    {
        return $this->data;
    }

    public function getSolvingStatistics()
    {
        return $this->solvingStatistics;
    }

    /**
     * @return \Nonogram\Solver\SolverJ54
     */
    private function getSolver()
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Config')));
        $loader->load('container.yml');
        $solver = $container->get('solver');
        return $solver;
    }

}
