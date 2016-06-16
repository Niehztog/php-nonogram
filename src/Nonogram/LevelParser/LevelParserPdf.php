<?php

namespace Nonogram\LevelParser;

class LevelParserPdf extends AbstractLevelParser implements LevelParserInterface, LevelParserMetaDataInterface
{

    /**
     * @var array
     */
    private $labelsRaw = array('columns' => array(), 'rows' => array());

    /**
     * @var array
     */
    private $gridLines = array('columns' => array(), 'rows' => array());

    /**
     * left outer boundary x-coordinate
     * @var
     */
    private $outerBorderLeftX;

    /**
     * right outer boundary x-coordinate
     *
     * @var
     */
    private $outerBorderRightX;

    /**
     * top outer boundary y-coordinate
     *
     * @var
     */
    private $outerBorderTopY;

    /**
     * bottom outer boundary y-coordinate
     *
     * @var
     */
    private $outerBorderBottomY;

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
     * @var \Nonogram\Label\Color\Factory
     */
    private $colorFactory;

    const REGEX_SNIPPET_FLOAT_NUMBER = '([0-9]+\.?[0-9]*)';

    const REGEX_OUTER_BORDERS = '~1\.5 w 0 0 0 RG ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' m ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' l ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' l ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' l ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' l ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' l ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' l S~';

    const REGEX_GRID_LINES = '~(?:1|0)\.5 w 0 0 0 RG ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' m ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' l S~';

    const REGEX_TEXT = '~Tf (?:' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' rg )?[0-9]+ [0-9]+ [0-9]+ [0-9]+ ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' Tm.+\((.+)\)~';

    //lower-left  corner  (x,y)  and  dimensions width and height in user space. The operation x y width height re
    const REGEX_HIDDEN_CLUES = '~' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' rg ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' ' . self::REGEX_SNIPPET_FLOAT_NUMBER . ' (\-?[0-9]+\.?[0-9]*) (\-?[0-9]+\.?[0-9]*) re f~';

    /**
     * List of all regex and the proper processing method for ther results
     * @var array
     */
    private static $knownContentTypes = array(
        'processOuterBorders' => self::REGEX_OUTER_BORDERS,
        'processGridLine' => self::REGEX_GRID_LINES,
        'processText' => self::REGEX_TEXT,
        'processHiddenClue' => self::REGEX_HIDDEN_CLUES,
    );

    /**
     * LevelParserPdf constructor.
     * @param \Nonogram\Label\Factory $labelFactory
     * @param \Nonogram\Label\Color\Factory $colorFactory
     */
    public function __construct(
        \Nonogram\Label\Factory $labelFactory,
        \Nonogram\Label\Color\Factory $colorFactory
    ) {
        parent::__construct($labelFactory);
        $this->colorFactory = $colorFactory;
    }

    /**
     * @param $rawData
     */
    public function setRawData($rawData)
    {
        parent::setRawData($rawData);
        $this->parseRawData();
    }

    /**
     * This method returns the supported file extension
     * @return string
     */
    public function getFileExtension()
    {
        return 'pdf';
    }

    /**
     * @return Label
     */
    public function getLabels()
    {
        return $this->labels;
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
     * @uses processOuterBorders
     * @uses processGridLine
     * @uses processText
     * @uses processHiddenClue
     * @throws \Exception
     */
    private function parseRawData()
    {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseContent($this->rawData);
        $pdfObjects = $pdf->getObjects();

        foreach ($pdfObjects as $key => $object) {
            $content = $object->getContent();
            if ('' === $content) {
                continue;
            }

            foreach(self::$knownContentTypes as $method => $regex) {
                if (1 === preg_match($regex, $content, $matches)) {
                    $this->$method($matches);
                    break;
                }
            }
        }

        $this->fillGaps();
        $this->labels = $this->labelFactory->getFromRaw($this->labelsRaw);
        $this->resetTempProperties();
    }

    /**
     * @param array $matches
     */
    private function processOuterBorders(array $matches)
    {
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

        $this->outerBorderLeftX = $lineX1;
        $this->outerBorderRightX = $lineX3;
        $this->outerBorderTopY = $lineY6;
        $this->outerBorderBottomY = $lineY4;
    }

    /**
     * @param array $matches
     */
    private function processGridLine(array $matches)
    {
        if (!isset($this->outerBorderLeftX)) {
            throw new \RuntimeException('pdf could not properly be parsed');
        }

        $lineX1 = $matches[1];
        $lineY1 = $matches[2];
        $lineX2 = $matches[3];
        $lineY2 = $matches[4];
        if ($lineX1 === $lineX2) {
            //vertical line
            $direction = 'columns';
            $newVal = $lineX1;
        } elseif ($lineY1 === $lineY2) {
            //horizontal line
            $direction = 'rows';
            $newVal = $lineY1;
        } else {
            trigger_error(sprintf('improper line detected in pdf: ($d/%d) (%d/%d) ', $lineX1, $lineY1, $lineX2, $lineY2), E_USER_WARNING);
            return;
        }

        $lastElement = end($this->gridLines[$direction]);
        if (false === $lastElement) {
            $this->gridLines[$direction][] = array(
                's' => ('columns' === $direction ? $this->outerBorderLeftX : $this->outerBorderTopY),
                'e' => $newVal
            );
        } else {
            $lastKey = key($this->gridLines[$direction]);
            $this->gridLines[$direction][$lastKey]['e'] = $newVal;
        }

        $this->gridLines[$direction][] = array(
            's' => $newVal,
            'e' => ('columns' === $direction ? $this->outerBorderRightX : $this->outerBorderBottomY)
        );
    }

    /**
     * @param array $matches
     */
    private function processText(array $matches)
    {
        $colorHex = $this->rgb2hex($matches[1] * 255, $matches[2] * 255, $matches[3] * 255);
        $coordsX = $matches[4];
        $coordsY = $matches[5];
        $text = $matches[6];

        if (!is_numeric($text) || in_array($coordsY, array('751', '732.80', '717.20', '701.60'))) {
            $this->processMetaData($coordsY, $text);
            return;
        }

        $this->processClue($colorHex, $coordsX, $coordsY, $text);
    }

    /**
     * @param $coordsY
     * @param $text
     */
    private function processMetaData($coordsY, $text)
    {
        if ('751' === $coordsY && 0 === strpos($text, 'Web Paint-by-Number Puzzle #')) {
            $this->id = (int)substr($text, 28);
        } elseif ('732.80' === $coordsY) {
            $this->title = $text;
        } elseif ('717.20' === $coordsY && 0 === strpos($text, 'created by ')) {
            $this->author = substr($text, 11);
        } elseif ('701.60' === $coordsY) {
            $this->created = $text;
        } elseif (0 === strpos($text, ' Copyright ')) {
            $this->copyright = '(c)' . $text;
        }
    }

    /**
     * @param array $matches
     */
    private function processHiddenClue(array $matches)
    {
        $colorHex = $this->rgb2hex($matches[1] * 255, $matches[2] * 255, $matches[3] * 255);
        $hiddenClueWidth = $matches[6];
        $hiddenClueHeight = $matches[7];
        $coordsX = sprintf("%01.2f", $matches[4] + $hiddenClueWidth / 2);
        $coordsY = sprintf("%01.2f", $matches[5] + $hiddenClueHeight - 1);
        $text = '0';

        $this->processClue($colorHex, $coordsX, $coordsY, $text);
    }

    /**
     * @param $colorHex
     * @param $coordsX
     * @param $coordsY
     * @param $text
     */
    private function processClue($colorHex, $coordsX, $coordsY, $text)
    {
        $color = $this->colorFactory->getFromHex($colorHex);

        if (!isset($this->gridLines['columns'][0]) || !isset($this->gridLines['rows'][0])) {
            throw new \RuntimeException('pdf could not properly be parsed');
        }

        //determine whether we deal with a row or column label
        if ($coordsX > $this->outerBorderLeftX && $coordsY > $this->outerBorderTopY) {
            //column label
            $direction = 'columns';
            $coord = $coordsX;
            $coordFitsInto = function ($direction, $currentKey, $coord) {
                return $coord > $this->gridLines[$direction][$currentKey]['s'] && $coord < $this->gridLines[$direction][$currentKey]['e'];
            };
        } elseif ($coordsX < $this->outerBorderLeftX && $coordsY < $this->outerBorderTopY) {
            //row label
            $direction = 'rows';
            $coord = $coordsY;
            $coordFitsInto = function ($direction, $currentKey, $coord) {
                return $coord < $this->gridLines[$direction][$currentKey]['s'] && $coord > $this->gridLines[$direction][$currentKey]['e'];
            };
        } else {
            trigger_error('found unparseable information in pdf file: ' . $text, E_USER_WARNING);
            return;
        }

        //does label fit into current sequence?
        $currentKey = count($this->labelsRaw[$direction]);
        $currentKey = 0 === $currentKey ? 0 : $currentKey - 1;
        for ($i = $currentKey; $i <= count($this->gridLines[$direction]); $i++) {
            if (!isset($this->gridLines[$direction][$i])) {
                throw new \RuntimeException('pdf could not properly be parsed');
            }
            if ($coordFitsInto($direction, $i, $coord)) {
                $this->labelsRaw[$direction][$i][] = new \Nonogram\Label\Count($text, $color);
                break;
            }
        }
    }

    /**
     * Fills gaps in label arrays with empty arrays
     */
    private function fillGaps()
    {
        foreach(array('columns', 'rows') as $direction) {
            for ($i = 0; $i < count($this->labelsRaw[$direction]); $i++) {
                if (!isset($this->labelsRaw[$direction][$i])) {
                    $this->labelsRaw[$direction][$i] = array();
                }
            }
            ksort($this->labelsRaw[$direction]);
        }
    }

    /**
     * reset temporary properties required during parsing
     */
    private function resetTempProperties()
    {
        $this->labelsRaw = array('columns' => array(), 'rows' => array());
        $this->gridLines = array('columns' => array(), 'rows' => array());
        $this->outerBorderLeftX = null;
        $this->outerBorderRightX = null;
        $this->outerBorderTopY = null;
        $this->outerBorderBottomY = null;
    }

    /**
     * Convert RGB to hex color
     *
     * @param $r
     * @param $g
     * @param $b
     * @return string
     */
    private function rgb2hex($r, $g, $b)
    {
        return sprintf('%02x%02x%02x', $r, $g, $b);
    }

}
