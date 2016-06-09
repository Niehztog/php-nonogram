<?php

namespace Nonogram\View;

/**
 * Class ViewYaml
 * @package Nonogram\View
 */
class ViewYaml extends AbstractView implements ViewInterface, ViewWritableInterface {

    private $yamlDumper;

    public function __construct(\Symfony\Component\Yaml\Dumper $yamlDumper)
    {
        $this->yamlDumper = $yamlDumper;
    }

    /**
     * @return string
     */
    public function drawField() {
        $labels = $this->grid->getLabels();
        $data = array('columns' => $labels->getCol(), 'rows' => $labels->getRow());
        $yaml = $this->yamlDumper->dump($data,2);
        return $yaml;
    }

    /**
     * In case output format supports being written to a file, this method returns the suitable file extension
     * @return string
     */
    public function getFileExtension()
    {
        return 'yml';
    }

}