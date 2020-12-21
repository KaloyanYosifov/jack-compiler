<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
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

it('compiles an array let statement declaration', function () {
    $implementation = 'let testing[23] = 35';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = LetStatementCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::LET_STATEMENT()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'let');
    assertComplexCompiledData($compiledData, 1, CompilationType::IDENTIFIER(), 'testing');
    assertComplexCompiledData($compiledData, 2, CompilationType::SYMBOL(), '[');
    assertComplexCompiledData($compiledData, 3, CompilationType::EXPRESSION());
    assertComplexCompiledData($compiledData, 4, CompilationType::SYMBOL(), ']');
    assertComplexCompiledData($compiledData, 5, CompilationType::SYMBOL(), '=');
    assertComplexCompiledData($compiledData, 6, CompilationType::EXPRESSION());

    $this->assertNull($compiledData->getDataFrom(7));
});

it('throws a syntax error', function (string $implementation) {
    $this->expectException(InvalidSyntaxError::class);
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    LetStatementCompilation::create()->compile($tokenizedData);
})
    ->with([
        'let testing[233 = "hehe"',
        'let testing 3213',
    ]);
