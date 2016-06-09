#!/usr/bin/env php
<?php

namespace Nonogram;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$rootDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;
require $rootDir . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(realpath(__DIR__.DIRECTORY_SEPARATOR.'Config')));
$loader->load('container.yml');
$container->setParameter('application.root_directory', $rootDir);
$container->compile();
$app = $container->get('application');
$app->run();
