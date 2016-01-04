<?php

namespace Nonogram\Label;
use Nonogram\Label\Provider\LabelProviderGrid;
use Nonogram\Label\Provider\LabelProviderFile;

class Factory
{

    /**
     * @param \Nonogram\Grid $grid
     * @return LabelProviderGrid
     */
    public function getForGrid(\Nonogram\Grid\Grid $grid)
    {
        return new LabelProviderGrid($grid);
    }

    /**
     * @param $fileName
     * @return LabelProviderFile
     */
    public function getForFile($fileName)
    {
        $fp = new LabelProviderFile();
        $fp->load($fileName);
        return $fp;
    }
}
