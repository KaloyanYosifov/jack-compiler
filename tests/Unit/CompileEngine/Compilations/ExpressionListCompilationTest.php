<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;

use JackCompiler\CompileEngine\Compilations\ExpressionListCompilation;

use function Tests\assertComplexCompiledData;

it('compiles an expression list', function () {
    $classImplementation = 'test, ~hacker, test = fest';
    $tokenizedData = Tokenizer::create()->handleStringData($classImplementation);
    $compiledData = ExpressionListCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::EXPRESSION_LIST()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::EXPRESSION());

    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), ',');
    assertComplexCompiledData($compiledData, 2, CompilationType::EXPRESSION());

    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), ',');
    assertComplexCompiledData($compiledData, 4, CompilationType::EXPRESSION());
});

it('throws a syntax error', function (string $implementation) {
    $this->expectException(InvalidSyntaxError::class);
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    ExpressionListCompilation::create()->compile($tokenizedData);
})
    ->with([
        'test ~hacker',
        'test, fest test',
    ]);
