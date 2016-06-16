<?php

namespace NonogramTests;

class LevelParserTest extends AbstractTestCase
{

    private \Nonogram\Label\Factory $labelFactory;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->labelFactory = new \Nonogram\Label\Factory(new \Nonogram\Label\LabelProviderCells());
        $this->labelFactory->setContainer($this->container);
    }

    public static function parserDataProvider() {
        $rawDataDat = <<<'EOD'
11000
11000
11000
11000
11111
EOD;

        $rawDataLua = <<<'EOD'
leveldata.gridsize = 5
leveldata.gamemode = "easy"
leveldata.number = "1"
leveldata.letter = "A"

irow1 =  {"O", "O", ".", ".", "."}
irow2 =  {"O", "O", ".", ".", "."}
irow3 =  {"O", "O", ".", ".", "."}
irow4 =  {"O", "O", ".", ".", "."}
irow5 =  {"O", "O", "O", "O", "O"}
EOD;

        $rawDataPdf = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'testdata'.DIRECTORY_SEPARATOR.'webpbn-1-1.pdf');

        $rawDataXml = <<<'EOD'
<?xml version="1.0"?>
<!DOCTYPE pbn SYSTEM "http://webpbn.com/pbn-0.3.dtd">
<puzzleset>
<source>webpbn.com</source>

<puzzle type="grid" defaultcolor="black">
<title>Demo Puzzle from Front Page</title>
<author>Jan Wolter</author>
<authorid>jan</authorid>
<copyright>(c) Copyright 2004 by Jan Wolter</copyright>
<id>#1 (v.1)</id>
<description>A stick figure man, dancing his stickly little heart out.</description>
<note>published,definitely unique,definitely line/color solvable</note>

<color name="white" char=".">FFFFFF</color>
<color name="black" char="X">000000</color>

<clues type="columns">
<line><count>2</count><count>1</count></line>
<line><count>2</count><count>1</count><count>3</count></line>
<line><count>7</count></line>
<line><count>1</count><count>3</count></line>
<line><count>2</count><count>1</count></line>
</clues>

<clues type="rows">
<line><count>2</count></line>
<line><count>2</count><count>1</count></line>
<line><count>1</count><count>1</count></line>
<line><count>3</count></line>
<line><count>1</count><count>1</count></line>
<line><count>1</count><count>1</count></line>
<line><count>2</count></line>
<line><count>1</count><count>1</count></line>
<line><count>1</count><count>2</count></line>
<line><count>2</count></line>
</clues>

</puzzle>
</puzzleset>
EOD;

        $rawDataYaml = <<<'EOD'
columns:
    - [5]
    - [5]
    - [1]
    - [1]
    - [1]
rows:
    - [2]
    - [2]
    - [2]
    - [2]
    - [5]
EOD;

        return [
            [\Nonogram\LevelParser\LevelParserDat::class, $rawDataDat, ['BBEEE', 'BBEEE', 'BBEEE', 'BBEEE', 'BBBBB']],
            [\Nonogram\LevelParser\LevelParserLua::class, $rawDataLua, ['BBEEE', 'BBEEE', 'BBEEE', 'BBEEE', 'BBBBB']],
            [\Nonogram\LevelParser\LevelParserPdf::class, $rawDataPdf, ['col'=>[0=>[0=>'2',1=>'1'],1=>[0=>'2',1=>'1',2=>'3'],2=>[0=>'7'],3=>[0=>'1',1=>'3'],4=>[0=>'2',1=>'1']],'row'=>[0=>[0=>'2'],1=>[0=>'2',1=>'1'],2=>[0=>'1',1=>'1'],3=>[0=>'3'],4=>[0=>'1',1=>'1'],5=>[0=>'1',1=>'1'],6=>[0=>'2'],7=>[0=>'1',1=>'1'],8=>[0=>'1',1=>'2'],9=>[0=>'2']]], ['getId' => 1,'getTitle' => 'Demo Puzzle from Front Page','getAuthor' => 'Jan Wolter','getCopyright' => '(c) Copyright 2004 by Jan Wolter','getDescription' => '','getCreated' => 'Mar 24, 2004']],
            [\Nonogram\LevelParser\LevelParserXml::class, $rawDataXml, ['col'=>[0=>[0=>'2',1=>'1'],1=>[0=>'2',1=>'1',2=>'3'],2=>[0=>'7'],3=>[0=>'1',1=>'3'],4=>[0=>'2',1=>'1']],'row'=>[0=>[0=>'2'],1=>[0=>'2',1=>'1'],2=>[0=>'1',1=>'1'],3=>[0=>'3'],4=>[0=>'1',1=>'1'],5=>[0=>'1',1=>'1'],6=>[0=>'2'],7=>[0=>'1',1=>'1'],8=>[0=>'1',1=>'2'],9=>[0=>'2']]], ['getId' => 1,'getTitle' => 'Demo Puzzle from Front Page','getAuthor' => 'Jan Wolter','getCopyright' => '(c) Copyright 2004 by Jan Wolter','getDescription' => 'A stick figure man, dancing his stickly little heart out.','getCreated' => '']],
            [\Nonogram\LevelParser\LevelParserYaml::class, $rawDataYaml, ['col'=>[0=>[0=>5],1=>[0=>5],2=>[0=>1],3=>[0=>1],4=>[0=>1]],'row'=>[0=>[0=>2],1=>[0=>2],2=>[0=>2],3=>[0=>2],4=>[0=>5]]]],
        ];

    }

    /**
     * @test
     *
     * @dataProvider parserDataProvider
     */
    public function testParser($className, $rawData, $expectedRaw, $expectedMetaData = [])
    {
        /** @var \Nonogram\LevelParser\AbstractLevelParser $parser */
        if(\Nonogram\LevelParser\LevelParserYaml::class === $className) {
            $parser = new $className($this->labelFactory, new \Symfony\Component\Yaml\Parser());
        }
        elseif('\Nonogram\LevelParser\LevelParserPdf' === $className) {
            $parser = new $className($this->labelFactory, $this->container->get('color_factory'));
        }
        else {
            $parser = new $className($this->labelFactory, $this->cellFactory);
        }
        $this->assertInstanceOf($className, $parser);
        $parser->setRawData($rawData);

        if($parser instanceof \Nonogram\LevelParser\AbstractLevelParserGrid) {
            $actual = $parser->getGrid();
            $expectedField = [];
            foreach($expectedRaw as $expectedRowRaw) {
                $expectedField[] = $this->convertRowRawToActual($expectedRowRaw);
            }
            $this->assertGridsEqual($expectedField, $actual, $className);
        }
        else {
            $labelsActual = $parser->getLabels();
            $this->assertEquals($expectedRaw['col'], $labelsActual->getCol());
            $this->assertEquals($expectedRaw['row'], $labelsActual->getRow());
        }

        if($parser instanceof \Nonogram\LevelParser\LevelParserMetaDataInterface && !empty($expectedMetaData)) {
            foreach($expectedMetaData as $method => $expectedResult) {
                $actualResult = $parser->$method();
                $this->assertEquals($expectedResult, $actualResult, $method);
            }
        }

    }

}