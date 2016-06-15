<?php

namespace Nonogram\Solver\RunRange;

/**
 * Class RunRangeFactory
 * @package Nonogram\Solver\RunRange
 */
class RunRangeFactory implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    /**
     * @param \Nonogram\Label\Label $labels
     * @return object
     */
    public function getForLabels(\Nonogram\Label\Label $labels)
    {
        $instance = $this->container->get('run_range');
        $instance->setLabels($labels);
        return $instance;
    }
    
}