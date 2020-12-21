<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;

use JackCompiler\CompileEngine\Compilations\Statements\ReturnStatementCompilation;

use function Tests\assertComplexCompiledData;

it('compiles a return statement', function () {
    $implementation = 'return;';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = ReturnStatementCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::RETURN_STATEMENT()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'return');
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), ';');

    $this->assertNull($compiledData->getDataFrom(2));
});

it('compiles a return statement with expression', function () {
    $implementation = 'return "testing";';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = ReturnStatementCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::RETURN_STATEMENT()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'return');
    assertComplexCompiledData($compiledData, 1, CompilationType::EXPRESSION());
    assertComplexCompiledData($compiledData, 2, CompilationType::SYMBOL(), ';');

    $this->assertNull($compiledData->getDataFrom(3));
});

it('throws a syntax error', function (string $implementation) {
    $this->expectException(InvalidSyntaxError::class);
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    ReturnStatementCompilation::create()->compile($tokenizedData);
})
    ->with([
        'return',
        'return test',
        'return "test"',
    ]);
