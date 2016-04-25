<?php

namespace Nonogram\Label\Provider;

use Symfony\Component\Yaml\Parser;

class LabelProviderFile implements  AnyLabelProvider
{
    private $data;

    /**
     * @param $fileName
     */
    public function load($fileName)
    {
        $yaml = new Parser();

        $this->data = $yaml->parse(file_get_contents($fileName));
    }

    /**
     * @return array
     */
    public function getLabelsForColumn()
    {
        return $this->data['columns'];
    }

    /**
     * @return array
     */
    public function getLabelsForRow()
    {
        return $this->data['rows'];
    }
}
