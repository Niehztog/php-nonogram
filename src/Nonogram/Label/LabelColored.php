<?php

namespace Nonogram\Label;

class LabelColored extends Label
{

    private $colNumbers;

    private $rowNumbers;

    private $colorListCache = array();

    /**
     * @var \Nonogram\Label\Color\Color
     */
    private $standardColor;

    /**
     * @param Color\Color $color
     */
    public function addStandardColor(\Nonogram\Label\Color\Color $color)
    {
        $this->standardColor = $color;
    }

    /**
     * Returns a list of all colors used in the labels
     * @return array
     */
    public function getColorList()
    {
        if(empty($this->colorListCache)) {
            if($this->standardColor instanceof \Nonogram\Label\Color\Color) {
                $this->colorListCache[] = $this->standardColor;
            }
            foreach(array('row', 'col') as $direction) {
                foreach($this->$direction as $sequence) {
                    foreach($sequence as $count) {
                        $color = $count->getColor();
                        if(!in_array($color, $this->colorListCache)) {
                            $this->colorListCache[] = $color;
                        }
                    }
                }
            }
        }
        return $this->colorListCache;
    }

    public function getColorCount()
    {
        return count($this->getColorList());
    }

    public function getColObjects()
    {
        return $this->col;
    }

    public function getRowObjects()
    {
        return $this->row;
    }
    
    /**
     * @return array
     */
    public function getCol()
    {
        if(empty($this->colNumbers)) {
            $this->initNumbers('col');
        }
        return $this->colNumbers;
    }

    /**
     * @return array
     */
    public function getRow()
    {
        if(empty($this->rowNumbers)) {
            $this->initNumbers('row');
        }
        return $this->rowNumbers;
    }

    private function initNumbers($direction)
    {
        $key = $direction . 'Numbers';
        $this->$key = array();
        foreach($this->$direction as $i => $seq) {
            $this->{$key}[$i] = array();
            foreach($seq as $j => $count) {
                $this->{$key}[$i][$j] = $count->getNumber();
            }
        }
    }

}