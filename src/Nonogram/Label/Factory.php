<?php

namespace Nonogram\Label;

use Nonogram\Label\Provider\LabelProviderGrid;
use Nonogram\Label\Provider\LabelProviderFile;

class Factory
{
    /**
     * @param \Nonogram\Grid $grid
     * @return Label
     */
    public function getForGrid(\Nonogram\Grid\Grid $grid)
    {
        return $this->createInstance(new LabelProviderGrid($grid));
    }

    /**
     * @param $fileName
     * @return Label
     */
    public function getForFile($fileName)
    {
        $fp = new LabelProviderFile();
        $fp->load($fileName);
        return $this->createInstance($fp);
    }

    /**
     * @param Provider\AnyLabelProvider $labelProvider
     * @return Label
     */
    private function createInstance(\Nonogram\Label\Provider\AnyLabelProvider $labelProvider)
    {
        $label = new Label();

        $labelsCol = $labelProvider->getLabelsForColumn();
        $labelsRow = $labelProvider->getLabelsForRow();

        $label->setCol($labelsCol);
        $label->setRow($labelsRow);

        return $label;
    }
}
