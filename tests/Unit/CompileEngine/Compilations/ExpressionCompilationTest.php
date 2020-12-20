<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\Compilations\ExpressionCompilation;

use function Tests\assertComplexCompiledData;

it('compiles an expression', function (string $implementation) {
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = ExpressionCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::EXPRESSION()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::TERM());

    $this->assertNull($compiledData->getDataFrom(1));
})
    ->with([
        '"test"',
        'test()',
        'JackClass.test()',
        'test[3]',
        '3132',
        'hacker',
        '(test)',
    ]);

it('compiles an expression with options', function (string $implementation, string $optionToExpect) {
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = ExpressionCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::EXPRESSION()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::TERM());
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), $optionToExpect);
    assertComplexCompiledData($compiledData, 2, CompilationType::TERM());

    $this->assertNull($compiledData->getDataFrom(3));
})
    ->with([
        [
            'hack + test',
            '+',
        ],
        [
            'hack - test',
            '-',
        ],
        [
            'hack * test',
            '*',
        ],
        [
            'hack / test',
            '/',
        ],
        [
            'hack & test',
            '&',
        ],
        [
            'hack | test',
            '|',
        ],
        [
            'hack < test',
            '<',
        ],
        [
            'hack > test',
            '>',
        ],
        [
            'hack = test',
            '=',
        ],
    ]);
