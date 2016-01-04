# PHP Nonogram

Implementation of the japanese logic puzzle game "nonogram" in PHP.
This has been made as a technical proof of concept with an educational focus. If you would like to "enjoy" playing the game you should go somewhere else.
It could be used as a base to implement a PHP driven server side implementation of the game.

## Features:
- 100% console application
- object-oriented software design
- load nonogram grid from file and display it in console
- makes use of nice ASCII arts for better visibility
- supports basic keyboard interaction for manual solving, press "b" to mark a cell as block and "e" to mark it as empty
 
## Future plans:
- implement automatic puzzle solving algorithms

## HowTo:
- start by: php src/Nonogram/console_application.php
- its recommended to execute it in a mingw(windows) or linux shell (for example bash)
- (windows command promt can't display special characters used)
- level files are stored in data/Levels (either yaml or binary format)
- yaml level files are used for unsolved puzzles, specified only by the number labels
- binary (.dat) files are used to store the graphical puzzle setup
- both are mutually exclusive, you can either load a .dat or .yml file
- to specify the level filename edit src/Nonogram/Application/ApplicationConsole.php

## feel free to contribute by forking and making merge requests...

Happy programming.