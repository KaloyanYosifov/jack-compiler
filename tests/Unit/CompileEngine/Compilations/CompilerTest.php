<?php

namespace Tests\Unit\Tokenizer;

use JackCompiler\Tokenizer\Tokenizer;

use JackCompiler\CompileEngine\Compiler;

use JackCompiler\Exporter\CompilationXMLExporter;

use function Tests\getTestFilePath;

it('it generates compiled data xml from jack file', function (string $jackFile, string $xmlCompareFile) {
    $compiler = new Compiler();
    $compiledData = $compiler->handle(Tokenizer::create()->handle(getTestFilePath($jackFile)));
    $tokenXMLExporter = new CompilationXMLExporter($compiledData);
    $compareFile = preg_replace('~\\r|\\r\\n|\\n|\s+~', '', file_get_contents(getTestFilePath($xmlCompareFile)));
    $exportedFile = preg_replace('~\\r|\\r\\n|\\n|\s+~', '', $tokenXMLExporter->export());
    $this->assertSame($compareFile, $exportedFile);
})
    ->with([
        [
            'jack/Main.jack',
            'xml-compiled/Main.xml',
        ],
        [
            'jack/Square.jack',
            'xml-compiled/Square.xml',
        ],
        [
            'jack/SquareGame.jack',
            'xml-compiled/SquareGame.xml',
        ],
    ]);
