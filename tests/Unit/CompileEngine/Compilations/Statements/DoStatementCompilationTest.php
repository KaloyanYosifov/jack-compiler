<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
use JackCompiler\CompileEngine\Compilations\Statements\DoStatementCompilation;

use function Tests\assertComplexCompiledData;

it('compiles a do statement', function (string $implementation) {
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = DoStatementCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::DO_STATEMENT()->equals($compiledData->getType()));
    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'do');
    assertComplexCompiledData($compiledData, 1, CompilationType::SUBROUTINE_CALL());

    $this->assertNull($compiledData->getDataFrom(2));
})
    ->with([
        'do test()',
        'do test(y, x)',
        'do JackClass.test()',
        'do JackClass.test(x, y)',
    ]);

it('throws a syntax error', function (string $implementation) {
    $this->expectException(InvalidSyntaxError::class);
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    DoStatementCompilation::create()->compile($tokenizedData);
})
    ->with([
        'do',
        'do rest',
        'do test(',
        'do JackClass.',
    ]);
