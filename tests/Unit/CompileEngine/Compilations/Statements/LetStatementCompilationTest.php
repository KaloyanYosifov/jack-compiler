<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\Compilations\Statements\LetStatementCompilation;

use function Tests\assertComplexCompiledData;

it('compiles a let statement declaration', function () {
    $implementation = 'let testing = 35';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = LetStatementCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::LET_STATEMENT()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'let');
    assertComplexCompiledData($compiledData, 1, CompilationType::IDENTIFIER(), 'testing');
    assertComplexCompiledData($compiledData, 2, CompilationType::SYMBOL(), '=');
    assertComplexCompiledData($compiledData, 3, CompilationType::EXPRESSION());

    $this->assertNull($compiledData->getDataFrom(4));
});
