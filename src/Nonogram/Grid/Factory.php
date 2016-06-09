<?php

namespace Nonogram\Grid;

class Factory implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    /**
     * @var \Nonogram\LevelParser\LevelParserFactory
     */
    private $parserFactory;

    /**
     * @var \Nonogram\LevelDataSource\LevelDataSourceFactory
     */
    private $dataSourceFactory;

    /**
     * Factory constructor.
     * @param \Nonogram\LevelParser\LevelParserFactory $parserFactory
     * @param \Nonogram\LevelDataSource\LevelDataSourceFactory $dataSourceFactory
     */
    public function __construct(
        \Nonogram\LevelParser\LevelParserFactory $parserFactory,
        \Nonogram\LevelDataSource\LevelDataSourceFactory $dataSourceFactory
    )
    {
        $this->parserFactory = $parserFactory;
        $this->dataSourceFactory = $dataSourceFactory;
    }

    /**
     * @param $urn
     * @return \Nonogram\Grid\Grid
     */
    public function get($urn)
    {
        if(empty($urn)) {
            throw new \InvalidArgumentException('missing parameter "urn"');
        }

        $dataSource = $this->dataSourceFactory->get($urn);
        $parser = $this->parserFactory->get($urn);
        $parser->setRawData($dataSource->getData());
        $cells = $parser->getGrid();
        $label = $parser->getLabels();

        $grid = $this->container->get('grid');
        $grid->setCells($cells);
        $grid->setLabels($label);
        $grid->setSolvingStatistics($parser->getSolvingStatistics());

        return $grid;
    }

}
