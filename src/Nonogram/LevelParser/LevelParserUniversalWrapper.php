<?php

namespace Nonogram\LevelParser;

class LevelParserUniversalWrapper extends AbstractLevelParser implements LevelParserGridInterface, LevelParserLabelInterface
{

    /**
     * @var LevelParserInterface
     */
    private $parser;

    /**
     * @var \Nonogram\Solver\SolverJ54
     */
    private $solver;

    /**
     * @var \Nonogram\Label\Factory
     */
    private $labelFactory;

    /**
     * @var array
     */
    private $solvingStatistics;

    /**
     * LevelParserUniversalWrapper constructor.
     * @param LevelParserInterface $parser
     */
    public function __construct(LevelParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param \Nonogram\Solver\SolverJ54 $solver
     */
    public function setSolver(\Nonogram\Solver\SolverJ54 $solver)
    {
        $this->solver = $solver;
    }

    /**
     * @param \Nonogram\Label\Factory $labelFactory
     */
    public function setLabelFactory(\Nonogram\Label\Factory $labelFactory)
    {
        $this->labelFactory = $labelFactory;
    }

    public function setRawData($rawData)
    {
        $this->parser->setRawData($rawData);
        parent::setRawData($rawData);
    }

    /**
     * @param string $urn
     * @return bool
     */
    public function canHandle($urn)
    {
        return $this->parser->canHandle($urn);
    }

    /**
     * @return Label
     */
    public function getLabels()
    {
        if($this->parser instanceof LevelParserLabelInterface) {
            return $this->parser->getLabels();
        }

        return $this->labelFactory->getForCells($this->parser->getGrid());
    }

    /**
     * @return \Nonogram\Cell\AnyCell[][]
     */
    public function getGrid()
    {
        if($this->parser instanceof LevelParserGridInterface) {
            return $this->parser->getGrid();
        }

        $label = $this->parser->getLabels();
        $cells = $this->solver->solve($label);
        $this->solvingStatistics = $this->solver->getSolvingStatistics();

        return $cells;
    }

    /**
     * @return array
     */
    public function getSolvingStatistics()
    {
        return $this->solvingStatistics;
    }

}
