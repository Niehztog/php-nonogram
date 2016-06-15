<?php

namespace Nonogram\Grid;

class Factory implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    /**
     * @var \Nonogram\LevelParser\Factory
     */
    private $parserFactory;

    /**
     * @var \Nonogram\LevelDataSource\Factory
     */
    private $dataSourceFactory;

    /**
     * Factory constructor.
     * @param \Nonogram\LevelParser\Factory $parserFactory
     * @param \Nonogram\LevelDataSource\Factory $dataSourceFactory
     */
    public function __construct(
        \Nonogram\LevelParser\Factory $parserFactory,
        \Nonogram\LevelDataSource\Factory $dataSourceFactory
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

        $grid = $this->container->get('grid');
        $grid->setCells($parser->getGrid());
        $grid->setLabels($parser->getLabels());

        if($parser instanceof \Nonogram\LevelParser\LevelParserMetaDataInterface) {
            $grid->setId($parser->getId());
            $grid->setTitle($parser->getTitle());
            $grid->setAuthor($parser->getAuthor());
            $grid->setCopyright($parser->getCopyright());
            $grid->setDescription($parser->getDescription());
            $grid->setCreated($parser->getCreated());
        }
        $title = $grid->getTitle();
        if(empty($title)) {
            $grid->setTitle(basename($urn));
        }

        return $grid;
    }

    /**
     * Returns list of all supported file extensions
     * @return array
     */
    public function getFileExtensions()
    {
        return $this->parserFactory->getFileExtensions();
    }

}
