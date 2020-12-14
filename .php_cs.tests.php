<?php

require __DIR__ . '/vendor/autoload.php';

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/tests');

$config = require __DIR__ . '/.php_cs.common.php';

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRules($config)
    ->setRiskyAllowed(true)
    ->setCacheFile(__DIR__ . '/.php_cs.tests.cache');