<?php

namespace Nonogram\View;

/**
 * Class ViewText
 * @package Nonogram\View
 */
class ViewText extends AbstractView implements ViewInterface
{
    
    /**
     * Length/width (in amount of chars) of the labels for horizontal rows
     * @var int
     */
    private $labelLength;

    /**
     * @var int
     */
    private $highlightPositionX;

    /**
     * @var int
     */
    private $highlightPositionY;

    /**
     * @var string
     */
    private $displayCustomMessage;

    /**
     * http://la.remifa.so/unicode/unicode.php?start=2500&end=257F
     * http://jonathonhill.net/2012-11-26/box-drawing-in-php/
     * @var string
     */
    const HTML_ENTITY_FORMAT = '&#x%s;';

    /**
     * top left corner
     * @var string
     */
    const BOX_DRAWINGS_LIGHT_DOWN_AND_RIGHT = '250c';

    /**
     * top right corner
     * @var string
     */
    const BOX_DRAWINGS_LIGHT_DOWN_AND_LEFT = '2510';

    /**
     * bottom left corner
     * @var string
     */
    const BOX_DRAWINGS_LIGHT_UP_AND_RIGHT = '2514';

    /**
     * bottom right corner
     * @var string
     */
    const BOX_DRAWINGS_LIGHT_UP_AND_LEFT = '2518';

    /**
     * vertical wall
     * @var string
     */
    const BOX_DRAWINGS_LIGHT_VERTICAL = '2502';

    /**
     * horizontal wall
     * @var string
     */
    const BOX_DRAWINGS_LIGHT_HORIZONTAL = '2500';

    /**
     *
     * @var string
     */
    const BOX_DRAWINGS_LIGHT_DOWN_AND_HORIZONTAL = '252c';

    /**
     *
     * @var string
     */
    const BOX_DRAWINGS_LIGHT_UP_AND_HORIZONTAL = '2534';

    /**
     *
     * @var string
     */
    const BOX_DRAWINGS_LIGHT_VERTICAL_AND_LEFT = '2524';

    /**
     *
     * @var string
     */
    const BOX_DRAWINGS_LIGHT_VERTICAL_AND_RIGHT = '251c';

    /**
     *
     * @var string
     */
    const BOX_DRAWINGS_LIGHT_VERTICAL_AND_HORIZONTAL = '253c';

    /**
     * top left corner
     * @var string
     */
    const BOX_DRAWINGS_DOUBLE_DOWN_AND_RIGHT = '2554';

    /**
     * top right corner
     * @var string
     */
    const BOX_DRAWINGS_DOUBLE_DOWN_AND_LEFT = '2557';

    /**
     * bottom left corner
     * @var string
     */
    const BOX_DRAWINGS_DOUBLE_UP_AND_RIGHT = '255a';

    /**
     * bottom right corner
     * @var string
     */
    const BOX_DRAWINGS_DOUBLE_UP_AND_LEFT = '255d';

    /**
     * vertical wall
     * @var string
     */
    const BOX_DRAWINGS_DOUBLE_VERTICAL = '2551';

    /**
     * horizontal wall
     * @var string
     */
    const BOX_DRAWINGS_DOUBLE_HORIZONTAL = '2550';

    /**
     *
     * @var string
     */
    const BOX_DRAWINGS_DOUBLE_DOWN_AND_HORIZONTAL = '2566';

    /**
     *
     * @var string
     */
    const BOX_DRAWINGS_DOUBLE_UP_AND_HORIZONTAL = '2569';

    /**
     *
     * @var string
     */
    const BOX_DRAWINGS_DOUBLE_VERTICAL_AND_LEFT = '2563';

    /**
     *
     * @var string
     */
    const BOX_DRAWINGS_DOUBLE_VERTICAL_AND_RIGHT = '2560';

    /**
     *
     * @var string
     */
    const BOX_DRAWINGS_DOUBLE_VERTICAL_AND_HORIZONTAL = '256c';

    /**
     * Used for marking an empty cell
     * @var string
     */
    const LIGHT_SHADE = '2591';

    /**
     * Used for displaying a "boxed" cell
     * @var string
     */
    const DARK_SHADE = '2593';
    
    /**
     * Amount of chars inside a cell
     * @var int
     */
    const CELL_WIDTH = 2;
    
    /**
     * @var array
     */
    private static $borderChars = array(
        'tl' => self::BOX_DRAWINGS_LIGHT_DOWN_AND_RIGHT,
        'tr' => self::BOX_DRAWINGS_LIGHT_DOWN_AND_LEFT,
        'bl' => self::BOX_DRAWINGS_LIGHT_UP_AND_RIGHT,
        'br' => self::BOX_DRAWINGS_LIGHT_UP_AND_LEFT,
        'v' => self::BOX_DRAWINGS_LIGHT_VERTICAL,
        'h' => self::BOX_DRAWINGS_LIGHT_HORIZONTAL,
        'hd' => self::BOX_DRAWINGS_LIGHT_DOWN_AND_HORIZONTAL,
        'hu' => self::BOX_DRAWINGS_LIGHT_UP_AND_HORIZONTAL,
        'vl' => self::BOX_DRAWINGS_LIGHT_VERTICAL_AND_LEFT,
        'vr' => self::BOX_DRAWINGS_LIGHT_VERTICAL_AND_RIGHT,
        'x' => self::BOX_DRAWINGS_LIGHT_VERTICAL_AND_HORIZONTAL,
    );

    /**
     * @var array
     */
    private static $borderCharsDouble = array(
        'tl' => self::BOX_DRAWINGS_DOUBLE_DOWN_AND_RIGHT,
        'tr' => self::BOX_DRAWINGS_DOUBLE_DOWN_AND_LEFT,
        'bl' => self::BOX_DRAWINGS_DOUBLE_UP_AND_RIGHT,
        'br' => self::BOX_DRAWINGS_DOUBLE_UP_AND_LEFT,
        'v' => self::BOX_DRAWINGS_DOUBLE_VERTICAL,
        'h' => self::BOX_DRAWINGS_DOUBLE_HORIZONTAL,
        'hd' => self::BOX_DRAWINGS_DOUBLE_DOWN_AND_HORIZONTAL,
        'hu' => self::BOX_DRAWINGS_DOUBLE_UP_AND_HORIZONTAL,
        'vl' => self::BOX_DRAWINGS_DOUBLE_VERTICAL_AND_LEFT,
        'vr' => self::BOX_DRAWINGS_DOUBLE_VERTICAL_AND_RIGHT,
        'x' => self::BOX_DRAWINGS_DOUBLE_VERTICAL_AND_HORIZONTAL,
    );

    public function __construct()
    {
        $this->initChars();
    }

    /**
     * @param int $highlightPositionX
     */
    public function setHighlightPositionX($highlightPositionX)
    {
        $this->highlightPositionX = $highlightPositionX;
    }

    /**
     * @param int $highlightPositionY
     */
    public function setHighlightPositionY($highlightPositionY)
    {
        $this->highlightPositionY = $highlightPositionY;
    }

    /**
     * @param string $displayCustomMessage
     */
    public function setDisplayCustomMessage($displayCustomMessage)
    {
        $this->displayCustomMessage = $displayCustomMessage;
    }

    /**
     * @return string
     */
    public function drawField()
    {

        $sizeX = $this->grid->getSizeX();
        $sizeY = $this->grid->getSizeY();
        $out = '';

        $out .= $this->getLabelsColumn();

        //draw grid
        $out .= str_repeat(' ', $this->labelLength);
        $out .= $this->getBorderChar('tl', $this->isHighlightedX(1) && $this->isHighlightedY(1));
        for ($x = 1; $x < $sizeX; $x++) {
            $out .= $this->getBorderChar('h', $this->isHighlightedX($x) && $this->isHighlightedY(1));
            $out .= $this->getBorderChar('hd', $this->isHighlightedY(1) && ($this->isHighlightedX($x) || $this->isHighlightedX($x + 1)));
        }
        $out .= $this->getBorderChar('h', $this->isHighlightedX($sizeX) && $this->isHighlightedY(1));
        $out .= $this->getBorderChar('tr', $this->isHighlightedX($sizeX) && $this->isHighlightedY(1));
        if (!empty($this->displayCustomMessage)) {
            $out .= $this->displayCustomMessage;
        }
        $out .= PHP_EOL;

        for ($y = 1; $y <= $sizeY; $y++) {
            $out .= $this->getLabelsRow($y);

            $out .= $this->getBorderChar('v', $this->isHighlightedY($y) && $this->isHighlightedX(1));

            for ($x = 1; $x <= $sizeX; $x++) {
                $cell = $this->grid->getCell($x, $y);
                $out .= $this->getCharForCell($cell);
                $out .= $this->getBorderChar('v', $this->isHighlightedY($y) && ($this->isHighlightedX($x) || $this->isHighlightedX($x + 1)));
            }

            $out .= PHP_EOL;

            if ($y < $sizeY) {
                $out .= str_repeat(' ', $this->labelLength);
                $out .= $this->getBorderChar('vr', $this->isHighlightedX(1) && ($this->isHighlightedY($y) || $this->isHighlightedY($y + 1)));
                for ($x = 1; $x < $sizeX; $x++) {
                    $out .= $this->getBorderChar('h', $this->isHighlightedX($x) && ($this->isHighlightedY($y) || $this->isHighlightedY($y + 1)));
                    $out .= $this->getBorderChar('x', ($this->isHighlightedY($y + 1) && ($this->isHighlightedX($x + 1) || $this->isHighlightedX($x)))
                        || ($this->isHighlightedY($y) && ($this->isHighlightedX($x + 1) || $this->isHighlightedX($x))));
                }
                $out .= $this->getBorderChar('h', $this->isHighlightedX($sizeX) && ($this->isHighlightedY($y) || $this->isHighlightedY($y + 1)));
                $out .= $this->getBorderChar('vl', $this->isHighlightedX($sizeX) && ($this->isHighlightedY($y) || $this->isHighlightedY($y + 1)));
                $out .= PHP_EOL;
            }
        }

        $out .= str_repeat(' ', $this->labelLength);
        $out .= $this->getBorderChar('bl', $this->isHighlightedX(1) && $this->isHighlightedY($sizeY));
        for ($x = 1; $x < $sizeX; $x++) {
            $out .= $this->getBorderChar('h', $this->isHighlightedX($x) && $this->isHighlightedY($sizeY));
            $out .= $this->getBorderChar('hu', $this->isHighlightedY($sizeY) && ($this->isHighlightedX($x) || $this->isHighlightedX($x + 1)));
        }
        $out .= $this->getBorderChar('h', $this->isHighlightedX($sizeX) && $this->isHighlightedY($sizeY));
        $out .= $this->getBorderChar('br', $this->isHighlightedX($sizeX) && $this->isHighlightedY($sizeY));
        $out .= PHP_EOL;

        return $out;
    }

    /**
     * @param \Nonogram\Grid\Grid $grid
     */
    public function setGrid(\Nonogram\Grid\Grid $grid)
    {
        parent::setGrid($grid);
        $this->initGridAttributes();
    }

    /**
     * Inits some variables which visually describe the playing field
     */
    private function initGridAttributes()
    {
        $this->labelLength = $this->getLabelLength();
    }
    
    /**
     *
     */
    private function initChars()
    {
        foreach (self::$borderChars as &$char) {
            $char = $this->getSpecialChar($char);
        }
        foreach (self::$borderCharsDouble as &$char) {
            $char = $this->getSpecialChar($char);
        }
    }

    /**
     * @param $char
     * @return string
     */
    private function getSpecialChar($char)
    {
        return html_entity_decode(sprintf(self::HTML_ENTITY_FORMAT, $char), ENT_NOQUOTES, 'UTF-8');
    }

    /**
     * @param $which
     * @param bool|false $double
     * @return mixed
     */
    private function getBorderChar($which, $double = false)
    {
        $multiplicator = 'h' === $which ? self::CELL_WIDTH : 1;

        if ($double) {
            $str = self::$borderCharsDouble[$which];
        } else {
            $str = self::$borderChars[$which];
        }

        return str_repeat($str, $multiplicator);
    }

    /**
     * @param $cell
     */
    private function getCharForCell(\Nonogram\Cell\AnyCell $cell)
    {
        if ($cell::STATUS_HIDDEN === $cell->getStatus() || $cell::TYPE_UNKNOWN === $cell->getType()) {
            $char = ' ';
        } else {
            if ($cell::TYPE_BOX === $cell->getType()) {
                $char = $this->getSpecialChar(self::DARK_SHADE);
            } else {
                $char = $this->getSpecialChar(self::LIGHT_SHADE);
            }
        }
        return str_repeat($char, self::CELL_WIDTH);
    }

    /**
     * @param $x
     * @return bool
     */
    private function isHighlightedX($x)
    {
        return $this->highlightPositionX == $x;
    }

    /**
     * @param $y
     * @return bool
     */
    private function isHighlightedY($y)
    {
        return $this->highlightPositionY == $y;
    }

    /**
     * draw column labels
     * @return string
     */
    private function getLabelsColumn()
    {
        $labels = $this->grid->getLabels();
        $maxNumbersCol = $labels->getMaxAmountVertical();
        $out = '';

        for ($line = $maxNumbersCol - 1; $line >= 0; $line--) {
            $out .= str_repeat(' ', $this->labelLength);
            for ($x = 1; $x <= $this->grid->getSizeX(); $x++) {
                $labelsCol = $labels->getLabelsForColumn($x);
                if (null === $labelsCol) {
                    throw new \RuntimeException('failed to retrieve labels for column '.$x);
                }
                $labelsColReverse = array_reverse($labelsCol);

                if (isset($labelsColReverse[$line])) {
                    $out .= ' ' . str_pad('0' === $labelsColReverse[$line] ? 'X' : $labelsColReverse[$line], self::CELL_WIDTH, ' ', STR_PAD_LEFT);
                } else {
                    $out .= ' ' . str_repeat(' ', self::CELL_WIDTH);
                }
            }
            $out .= PHP_EOL;
        }
        return $out;
    }

    /**
     * draw row labels
     * @return string
     */
    private function getLabelsRow($index)
    {
        $labels = $this->grid->getLabels();
        $maxNumbersRow = $labels->getMaxAmountHorizontal();
        $maxLength = $labels->getMaxDigitCount();
        $labelsRow = $labels->getLabelsForRow($index);

        $out = '';
        for ($column = 0; $column < $maxNumbersRow; $column++) {
            if (isset($labelsRow[$column])) {
                $out .= str_pad('0' === $labelsRow[$column] ? 'X' : $labelsRow[$column], $maxLength, ' ', STR_PAD_LEFT) . ' ';
            } else {
                $out = str_repeat(' ', $maxLength+1) . $out;
            }
        }

        return $out;
    }

    /**
     * @return int
     */
    private function getLabelLength()
    {
        $max = 0;
        for ($y = 1; $y <= $this->grid->getSizeY(); $y++) {
            $count = strlen($this->getLabelsRow($y));
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

}
