<?php

namespace Nonogram\Grid\Saver;

use Nonogram\View\ViewWritableInterface;

class GridSaverFile
{

    /**
     * @var \Nonogram\View\ViewInterface
     */
    private $view;

    /**
     * @param $fileName
     */
    public function save($fileName)
    {
        if(!file_exists(dirname($fileName))) {
            throw new \RuntimeException('Can\'t write to location ' . $fileName);
        }

        $data = $this->view->drawField();

        $res = file_put_contents($fileName, $data);
        if (false === $res) {
            throw new \RuntimeException(sprintf('File %s not written', $fileName));
        }
    }

    /**
     * Setter method for raw data
     *
     * @return array
     */
    public function setView(ViewWritableInterface $view)
    {
        $this->view = $view;
    }

}