#!/usr/bin/env php
<?php

namespace Nonogram;

$rootDir = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;
require $rootDir . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Nonogram\Application\ApplicationConsole;

$app = new ApplicationConsole();
$app->run();
