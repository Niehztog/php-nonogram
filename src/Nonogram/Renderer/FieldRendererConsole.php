<?php
//TODO: auftrennen in View/Controller, DataModel klarer abtrennen

namespace Nonogram\Renderer;

//use \Hoa\Console\Cursor;
use Nonogram\Exception\CellEmptyException;

/**
 * Class FieldRendererConsole
 * @package Nonogram\Renderer
 */
class FieldRendererConsole extends AbstractFieldRenderer implements AnyFieldRenderer
{
    /**
     * Maps key-constants (see below) to ASCII codes
     * @var array
     */
    private $keys;

    /**
     * Length/width (in amount of chars) of the labels for horizontal rows
     * @var int
     */
    private $labelLength;

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
     * @var int
     */
    const KEY_UP = 1;

    /**
     * @var int
     */
    const KEY_DOWN = 2;

    /**
     * @var int
     */
    const KEY_RIGHT = 3;

    /**
     * @var int
     */
    const KEY_LEFT = 4;

    /**
     * @var int
     */
    const KEY_BOX = 5;

    /**
     * @var int
     */
    const KEY_EMPTY = 6;

    /**
     * Amount of chars inside a cell
     * @var int
     */
    const CELL_WIDTH = 2;

    /**
     * @var int
     */
    private $currentPositionX = 1;

    /**
     * @var int
     */
    private $currentPositionY = 1;

    /**
     * Provide version which allows user interactions or merely display grid
     * @var bool
     */
    private $interactive = true;

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

    /**
     * @param $grid
     */
    public function __construct($grid)
    {
        parent::__construct($grid);
        $this->initGridAttributes();
        $this->initChars();
        $this->initKeys();
    }

    /**
     * @param boolean $interactive
     */
    public function setInteractive($interactive)
    {
        $this->interactive = $interactive;
    }

    /**
     * Inits some variables which visually describe the playing field
     */
    private function initGridAttributes()
    {
        $this->labelLength = $this->getLabelLength();
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
     * Creates and stores the mapping of key representing constants to ASCII codes
     */
    private function initKeys()
    {
        $keyUp = chr(27) . chr(91) . chr(65);
        $keyDown = chr(27) . chr(91) . chr(66);
        $keyRight = chr(27) . chr(91) . chr(67);
        $keyLeft = chr(27) . chr(91) . chr(68);
        $this->keys = array(
            self::KEY_UP => $keyUp,
            self::KEY_DOWN => $keyDown,
            self::KEY_RIGHT => $keyRight,
            self::KEY_LEFT => $keyLeft,
            self::KEY_BOX => 'b',
            self::KEY_EMPTY => 'e'
        );
    }

    /**
     *
     */
    public function render()
    {
        $sizeX = $this->grid->getSizeX();
        $sizeY = $this->grid->getSizeY();

        $this->drawField();

        if (!$this->interactive) {
            $statistics = $this->grid->getSolvingStatistics();
            if(!empty($statistics)) {
                echo 'Solving statistics:' . PHP_EOL;
                foreach ($statistics as $ruleClass => $ruleCounter) {
                    echo $ruleClass . ': ' . $ruleCounter.PHP_EOL;
                }
            }
            exit;
        }

        while ($key = $this->readKeys()) {
            switch ($key) {
                case self::KEY_UP:
                    if ($this->currentPositionY > 1) {
                        $this->currentPositionY--;
                    }
                    break;
                case self::KEY_DOWN:
                    if ($this->currentPositionY < $sizeY) {
                        $this->currentPositionY++;
                    }
                    break;
                case self::KEY_RIGHT:
                    if ($this->currentPositionX < $sizeX) {
                        $this->currentPositionX++;
                    }
                    break;
                case self::KEY_LEFT:
                    if ($this->currentPositionX > 1) {
                        $this->currentPositionX--;
                    }
                    break;
                case self::KEY_BOX:
                    $cell = $this->grid->getCell($this->currentPositionX, $this->currentPositionY);
                    try {
                        $cell->fill();
                        if ($this->grid->isSolved()) {
                            $this->drawWonScreen();
                            exit;
                        }
                    } catch (CellEmptyException $e) {
                        $this->incFailureCounter();
                    }
                    break;
                case self::KEY_EMPTY:
                    $cell = $this->grid->getCell($this->currentPositionX, $this->currentPositionY);
                    $cell->mark();
                    break;
            }

            $this->drawField();
        }
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
     *
     */
    private function drawField()
    {
        $sizeX = $this->grid->getSizeX();
        $sizeY = $this->grid->getSizeY();

        \Hoa\Console\Cursor::clear();

        //\Hoa\Console\Cursor::save();
        //echo PHP_EOL;
        //\Hoa\Console\Cursor::colorize('underlined foreground(yellow) background(#932e2e)');

        $this->drawLabelsColumn();


        //draw grid
        echo str_repeat(' ', $this->labelLength);
        echo $this->getBorderChar('tl', $this->isHighlightedX(1) && $this->isHighlightedY(1));
        for ($x = 1; $x < $sizeX; $x++) {
            echo $this->getBorderChar('h', $this->isHighlightedX($x) && $this->isHighlightedY(1));
            echo $this->getBorderChar('hd', $this->isHighlightedY(1) && ($this->isHighlightedX($x) || $this->isHighlightedX($x + 1)));
        }
        echo $this->getBorderChar('h', $this->isHighlightedX($sizeX) && $this->isHighlightedY(1));
        echo $this->getBorderChar('tr', $this->isHighlightedX($sizeX) && $this->isHighlightedY(1));
        if ($this->interactive) {
            echo str_repeat(' ', 4) . 'Failures: ' . $this->getFailureCounter();
        }
        echo PHP_EOL;

        for ($y = 1; $y <= $sizeY; $y++) {
            $this->drawLabelsRow($y);

            echo $this->getBorderChar('v', $this->isHighlightedY($y) && $this->isHighlightedX(1));

            for ($x = 1; $x <= $sizeX; $x++) {
                $cell = $this->grid->getCell($x, $y);
                echo $this->getCharForCell($cell);
                echo $this->getBorderChar('v', $this->isHighlightedY($y) && ($this->isHighlightedX($x) || $this->isHighlightedX($x + 1)));
            }

            echo PHP_EOL;

            if ($y < $sizeY) {
                echo str_repeat(' ', $this->labelLength);
                echo $this->getBorderChar('vr', $this->isHighlightedX(1) && ($this->isHighlightedY($y) || $this->isHighlightedY($y + 1)));
                for ($x = 1; $x < $sizeX; $x++) {
                    echo $this->getBorderChar('h', $this->isHighlightedX($x) && ($this->isHighlightedY($y) || $this->isHighlightedY($y + 1)));
                    echo $this->getBorderChar('x', ($this->isHighlightedY($y + 1) && ($this->isHighlightedX($x + 1) || $this->isHighlightedX($x)))
                        || ($this->isHighlightedY($y) && ($this->isHighlightedX($x + 1) || $this->isHighlightedX($x))));
                }
                echo $this->getBorderChar('h', $this->isHighlightedX($sizeX) && ($this->isHighlightedY($y) || $this->isHighlightedY($y + 1)));
                echo $this->getBorderChar('vl', $this->isHighlightedX($sizeX) && ($this->isHighlightedY($y) || $this->isHighlightedY($y + 1)));
                echo PHP_EOL;
            }
        }

        echo str_repeat(' ', $this->labelLength);
        echo $this->getBorderChar('bl', $this->isHighlightedX(1) && $this->isHighlightedY($sizeY));
        for ($x = 1; $x < $sizeX; $x++) {
            echo $this->getBorderChar('h', $this->isHighlightedX($x) && $this->isHighlightedY($sizeY));
            echo $this->getBorderChar('hu', $this->isHighlightedY($sizeY) && ($this->isHighlightedX($x) || $this->isHighlightedX($x + 1)));
        }
        echo $this->getBorderChar('h', $this->isHighlightedX($sizeX) && $this->isHighlightedY($sizeY));
        echo $this->getBorderChar('br', $this->isHighlightedX($sizeX) && $this->isHighlightedY($sizeY));
        echo PHP_EOL;

        //\Hoa\Console\Cursor::restore();
    }

    /**
     * http://stackoverflow.com/questions/3684367
     *
     * @return mixed
     */
    private function readKeys()
    {
        $term = shell_exec('stty -g');
        if (null === $term) {
            throw new \RuntimeException('This program can only be run in a linux or mingw shell');
        }
        system("stty -icanon");

        while ($c = fread(STDIN, 10)) {
            /*for($i=0;$i<strlen($c);$i++) {
                var_dump(ord($c{$i}));
            }*/
            if (in_array($c, $this->keys)) {
                break;
            } else {
                exit;
            }
        }
        $key = array_search($c, $this->keys);

        // Reset the tty back to the original configuration
        system("stty '" . $term . "' > /dev/null 2>&1");

        return $key;
    }

    /**
     * @param $x
     * @return bool
     */
    private function isHighlightedX($x)
    {
        return $this->currentPositionX == $x;
    }

    /**
     * @param $y
     * @return bool
     */
    private function isHighlightedY($y)
    {
        return $this->currentPositionY == $y;
    }

    /**
     * draw column labels
     */
    private function drawLabelsColumn()
    {
        $labels = $this->grid->getLabels();
        $maxNumbersCol = $labels->getMaxAmountVertical();

        for ($line = $maxNumbersCol - 1; $line >= 0; $line--) {
            echo str_repeat(' ', $this->labelLength);
            for ($x = 1; $x <= $this->grid->getSizeX(); $x++) {
                $labelsCol = $labels->getLabelsForColumn($x);
                if (null === $labelsCol) {
                    throw new \RuntimeException('failed to retrieve labels for column '.$x);
                }
                $labelsColReverse = array_reverse($labelsCol);

                if (isset($labelsColReverse[$line])) {
                    echo ' ' . str_pad($labelsColReverse[$line], self::CELL_WIDTH, ' ', STR_PAD_LEFT);
                } else {
                    echo ' ' . str_repeat(' ', self::CELL_WIDTH);
                }
            }
            echo PHP_EOL;
        }
    }

    /**
     * draw column labels
     * @return int
     */
    private function drawLabelsRow($index)
    {
        echo $this->getLabelsRow($index);
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

    /**
     * draw column labels
     * @return string
     */
    private function getLabelsRow($index)
    {
        $labels = $this->grid->getLabels();
        $maxNumbersRow = $labels->getMaxAmountHorizontal();
        $labelsRow = $labels->getLabelsForRow($index);
        $max = max($labelsRow);
        $maxLength = strlen((string)$max);

        $return = '';
        for ($column = 0; $column < $maxNumbersRow; $column++) {
            if (isset($labelsRow[$column])) {
                $return .= str_pad($labelsRow[$column], $maxLength, ' ', STR_PAD_LEFT) . ' ';
            } else {
                $return = str_repeat(' ', self::CELL_WIDTH) . $return;
            }
        }

        return $return;
    }

    /**
     * @param $cell
     */
    private function getCharForCell(\Nonogram\Cell\AnyCell $cell)
    {
        if (($this->interactive && $cell::STATUS_HIDDEN === $cell->getStatus()) || $cell::TYPE_UNKNOWN === $cell->getType()) {
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
     *
     */
    private function drawWonScreen()
    {
        echo PHP_EOL . 'Congratulations! You solved the puzzle. :)' . PHP_EOL;
    }
}
