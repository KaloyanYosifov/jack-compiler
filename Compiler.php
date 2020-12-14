<?php

use JackCompiler\Support\Helpers;

if (php_sapi_name() !== PHP_SAPI) {
    echo 'Please run this in the command line!';
    exit(1);
}

if ($argc === 1) {
    echo 'Please specify the vm file to translate.';
    exit(1);
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$baseDir = Helpers::getBaseDirFromFile($argv[1]);
$firstFileOrDirectory = $baseDir . $argv[1];
$generatedFileName = $baseDir . pathinfo($argv[1], PATHINFO_FILENAME) . '.asm';

if (is_dir($firstFileOrDirectory)) {
    $files = Helpers::getFilesFromDirectory($firstFileOrDirectory, 'vm');
    // remove the single dot in the end if the user chooses the directory to translate with the `.`
    $firstFileOrDirectory = preg_replace('~\.$~', '', $firstFileOrDirectory);
    $generatedFileName = $firstFileOrDirectory . DIRECTORY_SEPARATOR . pathinfo($firstFileOrDirectory, PATHINFO_FILENAME) . '.asm';
} else {
    $files = array_slice($argv, 1);
}

if (!$files) {
    throw new \InvalidArgumentException('There are no vm files in this directory!');
}

if (file_exists($generatedFileName)) {
    unlink($generatedFileName);
}
