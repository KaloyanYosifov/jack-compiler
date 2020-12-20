<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;

use JackCompiler\CompileEngine\Compilations\SubroutineCallCompilation;

use function Tests\assertComplexCompiledData;

it('compiles a subroutine call declaration', function () {
    $classImplementation = 'test()';
    $tokenizedData = Tokenizer::create()->handleStringData($classImplementation);
    $compiledData = SubroutineCallCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::SUBROUTINE_CALL()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::IDENTIFIER(), 'test');
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 2, CompilationType::SYMBOL(), ')');

    $this->assertNull($compiledData->getDataFrom(3));
});

it('compiles a subroutine class call decleration', function () {
    $classImplementation = 'JackClass.helloWorld()';
    $tokenizedData = Tokenizer::create()->handleStringData($classImplementation);
    $compiledData = SubroutineCallCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::SUBROUTINE_CALL()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::IDENTIFIER(), 'JackClass');
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), '.');
    assertComplexCompiledData($compiledData, 2, CompilationType::IDENTIFIER(), 'helloWorld');
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 4, CompilationType::SYMBOL(), ')');

    $this->assertNull($compiledData->getDataFrom(5));
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
