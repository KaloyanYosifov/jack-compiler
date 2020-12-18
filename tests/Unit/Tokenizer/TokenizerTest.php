<?php

namespace Tests\Unit\Tokenizer;

use JackCompiler\Tokenizer\Tokenizer;

use JackCompiler\Exporter\TokenXMLExporter;

use function Tests\getTestFilePath;

it('it generates tokens from jack file', function (string $jackFile, string $xmlCompareFile) {
    $tokenizedData = Tokenizer::create()->handle(getTestFilePath($jackFile));
    $tokenXMLExporter = new TokenXMLExporter($tokenizedData);
    $compareFile = preg_replace('~\\r|\\r\\n|\\n|\s+~', '', file_get_contents(getTestFilePath($xmlCompareFile)));
    $exportedFile = preg_replace('~\\r|\\r\\n|\\n|\s+~', '', $tokenXMLExporter->export());
    $this->assertSame($compareFile, $exportedFile);
})
    ->with([
        [
            'jack/Main.jack',
            'xml-tokens/MainT.xml',
        ],
        [
            'jack/Square.jack',
            'xml-tokens/SquareT.xml',
        ],
        [
            'jack/SquareGame.jack',
            'xml-tokens/SquareGameT.xml',
        ],
    ]);
