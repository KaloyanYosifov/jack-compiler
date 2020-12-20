<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;

use JackCompiler\CompileEngine\Compilations\SubroutineCallCompilation;

use function Tests\assertComplexCompiledData;

it('compiles a subroutine call declaration', function () {
    $implementation = 'test()';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = SubroutineCallCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::SUBROUTINE_CALL()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::IDENTIFIER(), 'test');
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 2, CompilationType::EXPRESSION_LIST());
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), ')');

    $this->assertNull($compiledData->getDataFrom(4));
});

it('compiles a subroutine class call declaration', function () {
    $implementation = 'JackClass.helloWorld()';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = SubroutineCallCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::SUBROUTINE_CALL()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::IDENTIFIER(), 'JackClass');
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), '.');
    assertComplexCompiledData($compiledData, 2, CompilationType::IDENTIFIER(), 'helloWorld');
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 4, CompilationType::EXPRESSION_LIST());
    assertComplexCompiledData($compiledData, 5, CompilationType::SYMBOL(), ')');

    $this->assertNull($compiledData->getDataFrom(6));
});

it('compiles a subroutine call declaration with parameter list', function () {
    $implementation = 'helloWorld(x, y, help)';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = SubroutineCallCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::SUBROUTINE_CALL()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::IDENTIFIER(), 'helloWorld');
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 2, CompilationType::EXPRESSION_LIST());
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), ')');
});

it('throws a syntax error', function (string $implementation) {
    $this->expectException(InvalidSyntaxError::class);
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    SubroutineCallCompilation::create()->compile($tokenizedData);
})
    ->with([
        'test)',
        'test.testing)',
        'Test.testing(',
        'Test.testing(x, y,)',
    ]);
