<?php

namespace Nonogram\LevelDataSource;

class LevelDataSourceURL extends AbstractLevelDataSource implements LevelDataSourceInterface
{

    /**
     * @param string $urn
     * @return bool
     */
    public function canHandle($urn)
    {
        if (!$fp = fopen($urn, 'r')) {
            return false;
        }

        $meta = stream_get_meta_data($fp);
        fclose($fp);
        return isset($meta['wrapper_data']) && 'HTTP/1.1 200 OK' === $meta['wrapper_data'][0];
    }

    /**
     * @return string
     */
    public function getData()
    {
        $raw = file_get_contents($this->urn);
        if (false === $raw) {
            throw new \RuntimeException(sprintf('Data could not be downloaded from URL %s', $this->url));
        }
        return $raw;
    }

}