<?php

use JackCompiler\Support\Helpers;
use JackCompiler\Reader\FileReader;
use JackCompiler\Parsers\LineParser;
use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\Tokenizer\TokensMap;
use JackCompiler\CompileEngine\Compiler;
use JackCompiler\Exceptions\InvalidSyntaxError;
use JackCompiler\Exporter\CompilationXMLExporter;

if (php_sapi_name() !== PHP_SAPI) {
    echo 'Please run this in the command line!';
    exit(1);
}

if ($argc === 1) {
    echo 'Please specify the jack file to translate.';
    exit(1);
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$baseDir = Helpers::getBaseDirFromFile($argv[1]);
$firstFileOrDirectory = $baseDir . $argv[1];
$generatedFileName = $baseDir . pathinfo($argv[1], PATHINFO_FILENAME) . '.xml';

if (is_dir($firstFileOrDirectory)) {
    $files = Helpers::getFilesFromDirectory($firstFileOrDirectory, 'jack');
    // remove the single dot in the end if the user chooses the directory to translate with the `.`
    $firstFileOrDirectory = preg_replace('~\.$~', '', $firstFileOrDirectory);
    $generatedFileName = $firstFileOrDirectory . DIRECTORY_SEPARATOR . pathinfo($firstFileOrDirectory, PATHINFO_FILENAME) . '.xml';
} else {
    $files = array_slice($argv, 1);
}

if (!$files) {
    throw new \InvalidArgumentException('There are no xml files in this directory!');
}

if (file_exists($generatedFileName)) {
    unlink($generatedFileName);
}

$lineParser = new LineParser();
$tokenizer = new Tokenizer(new FileReader($lineParser), new TokensMap(), $lineParser);
$compiler = new Compiler();

$xml = '';
foreach ($files as $file) {
    try {
        $xml .= (new CompilationXMLExporter($compiler->handle($tokenizer->handle($file))))->export();
    } catch (InvalidSyntaxError $exception) {
        echo $exception->getMessage() . PHP_EOL;
        exit;
    }
}

file_put_contents(count($files) > 1 ? 'Main.xml' : $generatedFileName, $xml);