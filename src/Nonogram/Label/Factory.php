<?php

namespace Nonogram\Label;

class Factory implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    /**
     * @var LabelProviderCells
     */
    private $labelProviderCells;

    /**
     * Factory constructor.
     * @param LabelProviderCells $labelProviderCells
     */
    public function __construct(LabelProviderCells $labelProviderCells)
    {
        $this->labelProviderCells = $labelProviderCells;
    }

    /**
     * @param array $cells
     * @return Label
     */
    public function getForCells(array $cells)
    {
        $labelsRaw = $this->labelProviderCells->generateLabels($cells);
        return $this->getFromRaw($labelsRaw);
    }

    /**
     * @param array $labelsRaw
     * @return \Nonogram\Label\Label
     */
    public function getFromRaw(array $labelsRaw)
    {
        if (empty($labelsRaw) || !isset($labelsRaw['columns']) || !isset($labelsRaw['rows'])) {
            throw new \InvalidArgumentException('empty label array');
        }

        /** @var \Nonogram\Label\Label $label */
        $label = $this->container->get('label');

        $label->setCol($labelsRaw['columns']);
        $label->setRow($labelsRaw['rows']);

        return $label;
    }

}
