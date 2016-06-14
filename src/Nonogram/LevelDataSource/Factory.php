<?php

namespace Nonogram\LevelDataSource;

class Factory
{

    /**
     * @var LevelDataSourceInterface[]
     */
    private $dataSources;

    /**
     * @param LevelDataSourceInterface $parser
     */
    public function attachDataSource(LevelDataSourceInterface $dataSource)
    {
        $this->dataSources[] = $dataSource;
    }

    /**
     * @param $urn
     * @return LevelDataSourceInterface
     */
    public function get($urn)
    {
        if(empty($urn)) {
            throw new \InvalidArgumentException('missing parameter "urn"');
        }
        
        foreach($this->dataSources as $dataSource) {
            if($dataSource->canHandle($urn)) {
                $dataSource->setUrn($urn);
                return $dataSource;
            }
        }
        throw new \RuntimeException('no suitable data source found for urn '.$urn);
    }

}