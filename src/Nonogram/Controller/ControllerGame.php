<?php

namespace Nonogram\Controller;

//use \Hoa\Console\Cursor;
use Nonogram\Exception\CellEmptyException;
use Nonogram\View\ViewText;

/**
 * Class ControllerGame
 * @package Nonogram\Controller
 */
class ControllerGame extends AbstractSubController implements AnyController
{

    /**
     * Counts failed attempts to unveil actual empty cells as boxes
     * @var int
     */
    private $failureCounter = 0;

    /**
     * Maps key-constants (see below) to ASCII codes
     * @var array
     */
    private $keys;

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
     * @var int
     */
    private $currentPositionX = 1;

    /**
     * @var int
     */
    private $currentPositionY = 1;

    /**
     * ControllerGame constructor.
     * @param \Nonogram\Grid\Factory $gridFactory
     * @param ViewText $view
     * @param \Symfony\Component\Finder\Finder $finder
     */
    public function __construct(
        \Nonogram\Grid\Factory $gridFactory,
        \Nonogram\View\ViewText $view,
        \Symfony\Component\Finder\Finder $finder
    )
    {
        parent::__construct($gridFactory, $view, $finder);
        $this->initKeys();
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
     * @param string $urn
     */
    protected function executeAction($urn)
    {

        $this->view->setHighlightPositionX($this->currentPositionX);
        $this->view->setHighlightPositionY($this->currentPositionY);

        $this->drawField();

        $sizeX = $this->grid->getSizeX();
        $sizeY = $this->grid->getSizeY();

        while ($key = $this->readKeys()) {
            switch ($key) {
                case self::KEY_UP:
                    if ($this->currentPositionY > 1) {
                        $this->decCurrentPositionY();
                    }
                    break;
                case self::KEY_DOWN:
                    if ($this->currentPositionY < $sizeY) {
                        $this->incCurrentPositionY();
                    }
                    break;
                case self::KEY_RIGHT:
                    if ($this->currentPositionX < $sizeX) {
                        $this->incCurrentPositionX();
                    }
                    break;
                case self::KEY_LEFT:
                    if ($this->currentPositionX > 1) {
                        $this->decCurrentPositionX();
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
     *
     */
    private function drawField()
    {
        \Hoa\Console\Cursor::clear();

        //\Hoa\Console\Cursor::save();
        //echo PHP_EOL;
        //\Hoa\Console\Cursor::colorize('underlined foreground(yellow) background(#932e2e)');

        echo $this->view->drawField();

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
     *
     */
    private function drawWonScreen()
    {
        echo PHP_EOL . 'Congratulations! You solved the puzzle. :)' . PHP_EOL;
    }

    /**
     *
     */
    protected function incFailureCounter()
    {
        $this->failureCounter++;
        $this->view->setDisplayCustomMessage(str_repeat(' ', 4) . 'Failures: ' . $this->getFailureCounter());
    }

    /**
     * @return int
     */
    protected function getFailureCounter()
    {
        return $this->failureCounter;
    }

    /**
     * @param int $currentPositionX
     */
    private function setCurrentPositionX($currentPositionX)
    {
        $this->currentPositionX = $currentPositionX;
        $this->view->setHighlightPositionX($this->currentPositionX);
    }

    /**
     * @param int $currentPositionY
     */
    private function setCurrentPositionY($currentPositionY)
    {
        $this->currentPositionY = $currentPositionY;
        $this->view->setHighlightPositionY($this->currentPositionY);
    }

    private function incCurrentPositionX()
    {
        $this->setCurrentPositionX($this->currentPositionX+1);
    }

    private function decCurrentPositionX()
    {
        $this->setCurrentPositionX($this->currentPositionX-1);
    }

    private function incCurrentPositionY()
    {
        $this->setCurrentPositionY($this->currentPositionY+1);
    }

    private function decCurrentPositionY()
    {
        $this->setCurrentPositionY($this->currentPositionY-1);
    }

}
