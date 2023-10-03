<?php

namespace Nonogram\View;

use Nonogram\Cell\AnyCell;

/**
 * Class ViewXml
 *
 * Outputs format as described in http://webpbn.com/pbn_fmt.html
 * Allows storing of *all* relevant information about puzzles and is
 * therefore the preferred storage method
 *
 * @package Nonogram\View
 */
class ViewXml extends AbstractView implements ViewInterface, ViewWritableInterface {

    /**
     * @var bool
     */
    private $settingIncludeSolution = true;

    /**
     * @return string
     */
    public function drawField() {
        $outStr = 
            '<?xml version="1.0"?>' . PHP_EOL .
            '<!DOCTYPE pbn SYSTEM "http://webpbn.com/pbn-0.3.dtd">' . PHP_EOL .
            PHP_EOL .
            '<puzzleset>' . PHP_EOL .
            PHP_EOL .
            '<puzzle type="grid" defaultcolor="black">' . PHP_EOL .
            PHP_EOL .
            '<source>webpbn.com</source>' . PHP_EOL .
            '<id>'.($this->grid->getId() > 0 ? '#'.$this->grid->getId():'').'</id>' . PHP_EOL .
            '<title>'.$this->xmlEscape($this->grid->getTitle()).'</title>' . PHP_EOL .
            '<author>'.$this->xmlEscape($this->grid->getAuthor()).'</author>' . PHP_EOL .
            '<authorid></authorid>' . PHP_EOL .
            '<copyright>'.str_replace('(c) ', '&copy; ', $this->xmlEscape($this->grid->getCopyright())).'</copyright>' . PHP_EOL;
        if($this->grid->getDescription()) {
            $outStr .= '<description>' . PHP_EOL .
                $this->xmlEscape($this->grid->getDescription()) . PHP_EOL .
            '</description>' . PHP_EOL;
        }
        $outStr .= PHP_EOL .
        $labels = $this->grid->getLabels();
        if($labels instanceof \Nonogram\Label\LabelColored) {
            $colorList = $labels->getColorList();
            foreach($colorList as $colorKey => $color) {
                $outStr .=
                    '<color name="'.$color->getName().'" char="'.$color->getDefaultChar().'">'.$color->getHex().'</color>' . PHP_EOL;
            }
        }
        $outStr .=
            PHP_EOL .
            '<clues type="columns">' . PHP_EOL;

        $cols = $labels instanceof \Nonogram\Label\LabelColored ? $labels->getColObjects() : $labels->getCol();
        foreach($cols as $col) {
            if(empty($col)) {
                $outStr .= '<line></line>' . PHP_EOL;
                continue;
            }
            $outStr .= '<line>' . PHP_EOL;
            foreach($col as $count) {
                $outStr .= '<count'.($count instanceof \Nonogram\Label\Count ? ' color="'.$count->getColor()->getName().'"' : '').'>' . ($count instanceof \Nonogram\Label\Count ? $count->getNumber() : $count) . '</count>' . PHP_EOL;
            }
            $outStr .= '</line>' . PHP_EOL;
        }

        $outStr .=
            '</clues>' . PHP_EOL .
            PHP_EOL .
            '<clues type="rows">' . PHP_EOL;

        $rows = $labels instanceof \Nonogram\Label\LabelColored ? $labels->getRowObjects() : $labels->getRow();
        foreach($rows as $row) {
            if(empty($row)) {
                $outStr .= '<line></line>' . PHP_EOL;
                continue;
            }
            $outStr .= '<line>' . PHP_EOL;
            foreach($row as $count) {
                $outStr .= '<count'.($count instanceof \Nonogram\Label\Count ? ' color="'.$count->getColor()->getName().'"' : '').'>' . ($count instanceof \Nonogram\Label\Count ? $count->getNumber() : $count) . '</count>' . PHP_EOL;
            }
            $outStr .= '</line>' . PHP_EOL;
        }

        $outStr .= '</clues>' . PHP_EOL;
        if($this->settingIncludeSolution && $this->grid->isSolved()) {
            $outStr .= PHP_EOL .
            '<solution type="goal">' . PHP_EOL .
            '<image>' . PHP_EOL;

            $field = $this->grid->getCells();
            foreach ($field as $row) {
                $outStr .= '|';
                foreach ($row as $cell) {
                    $color = $cell->getColor();
                    $outStr .= $color instanceof \Nonogram\Label\Color\Color ? $color->getDefaultChar() : ($cell->getType() === AnyCell::TYPE_BOX ? 'X' : '.');
                }
                $outStr .= '|' . PHP_EOL;
            }

            $outStr .=
                '</image>' . PHP_EOL .
                '</solution>' . PHP_EOL;
        }
        $outStr .=
            PHP_EOL .
            '</puzzle>' . PHP_EOL .
            '</puzzleset>';


        return $outStr;
    }

    /**
     * @param null $newVal
     * @return bool
     */
    public function settingIncludeSolution($newVal = null)
    {
        if(is_bool($newVal)) {
            $this->settingIncludeSolution = $newVal;
        }
        else {
            return $this->settingIncludeSolution;
        }
    }

    /**
     * In case output format supports being written to a file, this method returns the suitable file extension
     * @return string
     */
    public function getFileExtension()
    {
        return 'xml';
    }

    /**
     * @param $string
     * @return string
     */
    private function xmlEscape($string)
    {
        return htmlspecialchars($string, ENT_XML1);
    }

}