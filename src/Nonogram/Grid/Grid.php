<?php

namespace Nonogram\Grid;

/**
 * Class Grid
 * @package Nonogram\Grid
 */
class Grid
{
    
    /**
     * 2-dimensional array
     * 1st dimension corresponds to the y-coordinate
     * 2nd dimension corresponds to the x-coordinate
     *
     * the smallest coordinate x- and y-wise is located in the upper left corner
     * the largest coordinate x- and y-wise is located in the lower right corner
     *
     * @var array
     */
    private $cells = array();

    /**
     * @var \Nonogram\Label\Label
     */
    private $labels;

    /**
     * Meta data - puzzle id (numeric)
     * @var int
     */
    private $id = 0;

    /**
     * Meta data - puzzle title
     * @var string
     */
    private $title = '';

    /**
     * Meta data - puzzle author
     * @var string
     */
    private $author = '';

    /**
     * Meta data - puzzle copyright
     * @var string
     */
    private $copyright = '';

    /**
     * Meta data - puzzle description
     * @var string
     */
    private $description = '';

    /**
     * Meta data - puzzle created
     * @var string
     */
    private $created = '';
        
    /**
     * @var \Nonogram\Solver\SolverJ54
     */
    private $solver;

    /**
     * @var array
     */
    private $solvingStatistics;


    /**
     * @param \Nonogram\Solver\SolverJ54 $solver
     */
    public function setSolver(\Nonogram\Solver\SolverJ54 $solver)
    {
        $this->solver = $solver;
    }

    /**
     * @param array $cells
     */
    public function setCells(array $cells)
    {
        $this->cells = $cells;
    }

    /**
     * Getter for all cells
     * @return AnyCell[][]
     */
    public function getCells()
    {
        if(empty($this->cells) && !empty($this->labels)) {
            $this->cells = $this->solver->solve($this->labels);
            $this->solvingStatistics = $this->solver->getSolvingStatistics();
        }

        return $this->cells;
    }

    /**
     * @param \Nonogram\Label\Label $label
     */
    public function setLabels(\Nonogram\Label\Label $label)
    {
        $this->labels = $label;
    }

    /**
     * @return \Nonogram\Label\Label
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     *
     *
     * @param $x
     * @param $y
     * @return mixed
     */
    public function getCell($x, $y)
    {
        $cells = $this->getCells();

        if (!isset($cells[$y-1][$x-1])) {
            throw new \OutOfRangeException(sprintf('No cell at %d:%d', $x, $y));
        }
        return $cells[$y-1][$x-1];
    }

    /**
     * Returns the number of columns (horizontal size)
     *
     * required for drawing the field
     */
    public function getSizeX()
    {
        return $this->labels->getSizeX();
    }

    /**
     * Returns the number of rows (vertical size)
     *
     * required for drawing the field
     */
    public function getSizeY()
    {
        return $this->labels->getSizeY();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * @param string $copyright
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Tells whether all cells are unveiled to their true state
     * @return bool
     */
    public function isSolved()
    {
        $cells = $this->getCells();
        foreach ($cells as $y => $row) {
            foreach ($row as $x => $cell) {
                if (!$cell->isSolved()) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getSolvingStatistics()
    {
        return $this->solvingStatistics;
    }

}
