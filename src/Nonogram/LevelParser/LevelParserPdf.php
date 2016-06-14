<?php

namespace Nonogram\LevelParser;

class LevelParserPdf extends AbstractLevelParser implements LevelParserInterface, LevelParserMetaDataInterface
{
    /**
     * @var \Nonogram\Label\Label
     */
    private $labels;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $copyright;

    /**
     * @var string
     */
    private $created;

    /**
     * @param $rawData
     */
    public function setRawData($rawData)
    {
        parent::setRawData($rawData);
        $this->parseRawData();
    }

    /**
     * @return Label
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @throws \Exception
     */
    private function parseRawData()
    {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseContent($this->rawData);

        $pdfObjects = $pdf->getObjects();
        $lastX = 0;
        $array = array();
        $direction = 'columns';
        $previousCoordX = 0;
        $previousCoordY = 0;
        $smallestDistanceX = 1024;
        $smallestDistanceY = 1024;
        foreach ($pdfObjects as $key => $object) {
            $content = $object->getContent();
            if ('' === $content) {
                continue;
            }

            //detect lines
            if (1 === preg_match('~1\.5 w 0 0 0 RG ([0-9]+\.?[0-9]*) ([0-9]+\.?[0-9]*) m ([0-9]+\.?[0-9]*) ([0-9]+\.?[0-9]*) l ([0-9]+\.?[0-9]*) ([0-9]+\.?[0-9]*) l ([0-9]+\.?[0-9]*) ([0-9]+\.?[0-9]*) l ([0-9]+\.?[0-9]*) ([0-9]+\.?[0-9]*) l ([0-9]+\.?[0-9]*) ([0-9]+\.?[0-9]*) l ([0-9]+\.?[0-9]*) ([0-9]+\.?[0-9]*) l S~', $content, $matches)) {
                $lineX1 = $matches[1];
                //$lineY1 = $matches[2];
                //$lineX2 = $matches[3];
                //$lineY2 = $matches[4];
                $lineX3 = $matches[5];
                //$lineY3 = $matches[6];
                //$lineX4 = $matches[7];
                $lineY4 = $matches[8];
                //$lineX5 = $matches[9];
                //$lineY5 = $matches[10];
                //$lineX6 = $matches[11];
                $lineY6 = $matches[12];

                $leftmostX = $lineX1;
                $rightmostX = $lineX3;
                $highestY = $lineY6;
                $lowestY = $lineY4;
                continue;
            }

            preg_match('~Tf (?:([0-9]+\.?[0-9]*) ([0-9]+\.?[0-9]*) ([0-9]+\.?[0-9]*) rg )?[0-9]+ [0-9]+ [0-9]+ [0-9]+ ([0-9]+\.?[0-9]+) ([0-9]+\.?[0-9]+) Tm.+\((.+)\)~', $content, $matches);
            if (!isset($matches[6])) {
                continue;
            }

            if ($matches[1] != 0 || $matches[2] != 0 || $matches[3] != 0) {
                throw new \RuntimeException('puzzle relies on colors - not supported');
            }

            $coordsX = $matches[4];
            $coordsY = $matches[5];
            $text = $matches[6];

            if (!is_numeric($text)) {
                if ('751' === $coordsY && 0 === strpos($text, 'Web Paint-by-Number Puzzle #')) {
                    $this->id = (int) substr($text, 28);
                } elseif ('732.80' === $coordsY) {
                    $this->title = $text;
                } elseif ('717.20' === $coordsY && 0 === strpos($text, 'created by ')) {
                    $this->author = substr($text, 11);
                } elseif ('701.60' === $coordsY) {
                    $this->created = $text;
                } elseif (0 === strpos($text, ' Copyright ')) {
                    $this->copyright = '(c)' . $text;
                }

                continue;
            }

            if ($coordsX >= $lastX) {
                $lastX = $coordsX;
            } else {
                //we have increasing x-coordinates, as soon as a lower x coordinate appears the rows begin
                $direction = 'rows';
            }

            if ('columns' === $direction) {
                //vertical labels: texts with same x-coordinate belong to the same column
                $array[$direction][$coordsX][] = $text;

                if ($previousCoordX > 0 && $coordsX - $previousCoordX > 0 && $coordsX - $previousCoordX < $smallestDistanceX) {
                    $smallestDistanceX = $coordsX - $previousCoordX;
                }
                $previousCoordX = $coordsX;
            } else {
                //horizontal labels: texts with same y-coordinate belong to the same row
                $array[$direction][$coordsY][] = $text;

                if ($previousCoordY > 0 && $previousCoordY - $coordsY > 0 && $previousCoordY - $coordsY < $smallestDistanceY) {
                    $smallestDistanceY = $previousCoordY - $coordsY;
                }
                $previousCoordY = $coordsY;
            }
        }

        //find gaps
        $previousCoordX = 0;
        foreach ($array['columns'] as $key => $val) {
            if ($previousCoordX > 0 && floor($key) - floor($previousCoordX + $smallestDistanceX) > 1) {
                for ($i=$previousCoordX + $smallestDistanceX;floor($i)<floor($key);$i=$i+$smallestDistanceX) {
                    $this->array_insert_before($array['columns'], $key, array((string)$i => array()));
                }
            }
            $previousCoordX = $key;
        }
        $previousCoordY = 0;
        foreach ($array['rows'] as $key => $val) {
            if ($previousCoordY > 0 && floor($previousCoordY - $smallestDistanceY) - floor($key) > 1) {
                for ($i=$previousCoordY - $smallestDistanceY;floor($i)>floor($key);$i=$i-$smallestDistanceY) {
                    $this->array_insert_before($array['rows'], $key, array((string)$i => array()));
                }
            }
            $previousCoordY = $key;
        }

        $columnKeys = array_keys($array['columns']);
        $columnsLeftmostX = min($columnKeys);
        $columnsRightmostX = max($columnKeys);
        for ($i=1;$i<=($columnsLeftmostX-$leftmostX)/$smallestDistanceX;$i++) {
            array_unshift($array['columns'], array());
        }
        for ($i=1;$i<=($rightmostX - $columnsRightmostX)/$smallestDistanceX;$i++) {
            array_push($array['columns'], array());
        }
        $rowKeys = array_keys($array['rows']);
        $rowsLowestY = min($rowKeys);
        $rowsHighestY = max($rowKeys);
        for ($i=1;$i<=($highestY - $rowsHighestY)/$smallestDistanceY;$i++) {
            array_unshift($array['rows'], array());
        }
        for ($i=1;$i<=($rowsLowestY-$lowestY)/$smallestDistanceY;$i++) {
            array_push($array['rows'], array());
        }
        
        //erase coordinates in array keys
        $array['columns'] = array_values($array['columns']);
        $array['rows'] = array_values($array['rows']);

        $this->labels = $this->labelFactory->getFromRaw($array);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Insert a value or key/value pair before a specific key in an array.
     *
     * @param array $array
     * @param string $key
     * @param array $new
     *
     * @return array
     */
    private function array_insert_before(array &$array, $key, array $new)
    {
        $keys = array_keys($array);
        $index = array_search($key, $keys);
        $pos = false === $index ? count($array) : $index;
        $array = array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
    }

    /**
     * This method returns the supported file extension
     * @return string
     */
    public function getFileExtension()
    {
        return 'pdf';
    }
}
