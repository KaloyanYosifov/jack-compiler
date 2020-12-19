<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
use JackCompiler\CompileEngine\Compilations\ParameterListCompilation;

use function Tests\assertComplexCompiledData;

it('compiles parameter list declaration', function () {
    $classImplementation = 'int hacker, char testing, boolean toast, JackClass jack';
    $tokenizedData = Tokenizer::create()->handleStringData($classImplementation);
    $compiledData = ParameterListCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::PARAMETER_LIST()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'int');
    assertComplexCompiledData($compiledData, 1, CompilationType::IDENTIFIER(), 'hacker');

    assertComplexCompiledData($compiledData, 2, CompilationType::SYMBOL(), ',');
    assertComplexCompiledData($compiledData, 3, CompilationType::KEYWORD(), 'char');
    assertComplexCompiledData($compiledData, 4, CompilationType::IDENTIFIER(), 'testing');

    assertComplexCompiledData($compiledData, 5, CompilationType::SYMBOL(), ',');
    assertComplexCompiledData($compiledData, 6, CompilationType::KEYWORD(), 'boolean');
    assertComplexCompiledData($compiledData, 7, CompilationType::IDENTIFIER(), 'toast');

    assertComplexCompiledData($compiledData, 8, CompilationType::SYMBOL(), ',');
    assertComplexCompiledData($compiledData, 9, CompilationType::KEYWORD(), 'JackClass');
    assertComplexCompiledData($compiledData, 10, CompilationType::IDENTIFIER(), 'jack');
});

it('throws a syntax error', function (string $implementation) {
    $this->expectException(InvalidSyntaxError::class);
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    ParameterListCompilation::create()->compile($tokenizedData);
})
    ->with([
        'int, char testing',
        'int',
        'int test, char',
    ]);
