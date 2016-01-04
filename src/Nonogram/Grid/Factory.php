<?php

namespace Nonogram\Grid;
use Nonogram\Grid\Provider\GridProviderFile;
use Nonogram\Grid\Provider\GridProviderLabel;

class Factory
{

    /**
     * @var int
     */
    const SOURCE_LABEL = 1;

    /**
     * @var int
     */
    const SOURCE_GRID = 2;

    /**
     * Indicated the source of the just loaded grid
     * @var
     */
    private $loadedSource;


    /**
     * @return mixed
     */
    public function getLoadedSource()
    {
        return $this->loadedSource;
    }

    public function getFromFile($filePathFull)
    {
        $ext = pathinfo($filePathFull, PATHINFO_EXTENSION);

        switch ($ext) {
            case 'dat':
                return $this->getFromGridFile($filePathFull);
                break;

            case 'yml':
                return $this->getFromLabelFile($filePathFull);
                break;
        }
    }

    /**
     * Loads a grid layout from file
     *
     * @param $filePathFull
     * @return \Nonogram\Grid\Grid
     */
    private function getFromGridFile($filePathFull)
    {
        $p = new GridProviderFile();
        $p->load($filePathFull);

        $grid = new Grid();
        $grid->setCells($p);

        $labelFactory = new \Nonogram\Label\Factory();
        $labelProvider = $labelFactory->getForGrid($grid);
        $grid->setLabels($labelProvider);

        $this->loadedSource = self::SOURCE_GRID;
        return $grid;
    }

    /**
     * Loads a grid label layout from file
     * (the actual grid has to be computed)
     *
     * @param $filePathFull
     * @return \Nonogram\Grid\Grid
     */
    private function getFromLabelFile($filePathFull)
    {
        $labelFactory = new \Nonogram\Label\Factory();
        $labelProvider = $labelFactory->getForFile($filePathFull);

        $p = new GridProviderLabel();
        $p->setLabels($labelProvider);

        $grid = new Grid();
        $grid->setCells($p);
        $grid->setLabels($labelProvider);

        $this->loadedSource = self::SOURCE_LABEL;
        return $grid;
    }

}