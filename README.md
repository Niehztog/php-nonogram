[![Build Status](https://travis-ci.org/Niehztog/php-nonogram.svg?branch=master)](https://travis-ci.org/Niehztog/php-nonogram)

# PHP Nonogram

Implementation of the japanese logic puzzle game "nonogram" in PHP.
This has been made as a technical proof of concept with an educational focus. If you would like to "enjoy" playing the game you should go somewhere else.
It could be used as a base to implement a PHP driven server side implementation of the game.

## Features:
- 100% console application
- object-oriented software design
- uses composer and symphony container
- load nonogram grid from file and display it in console
- makes use of nice ASCII arts for better visibility
- supports basic keyboard interaction for manual solving, press "b" to mark a cell as block and "e" to mark it as empty
- supports automatic puzzle solving using logical rules described in [\[1\]](http://debut.cis.nctu.edu.tw/Publications/pdfs/J54.pdf "An efficient algorithm for solving nonograms") \(works for simpler nonograms only\)
- supports html export of puzzles
- application fully configurable via configuration file (Nonogram/Config/container.yml)
- unittest coverage of most important code areas
- support for writing nonogram puzzles to different file formats
 - supported input formats: *.xml, *.yml, *.dat, *.pdf
 - supported output formats: *.xml, *.yml, *.dat, *.html

## Future plans:
- implement 'chronological backtracking' algorithm for autosolving more complex puzzles

## HowTo:
- start by: php src/Nonogram/console_application.php
- its recommended to execute it in a mingw(windows) or linux shell (for example bash)
- (windows command promt can't display special characters used)
- level files are stored in data/Levels (either yaml or text format)
- yaml level files are used for unsolved puzzles, specified only by the number labels
- text (.dat) files are used to store the graphical puzzle setup
- to specify the level filename edit the config file in Nonogram/Config/container.yml

## feel free to contribute by forking and making merge requests...

Happy programming.

[1] http://debut.cis.nctu.edu.tw/Publications/pdfs/J54.pdf
