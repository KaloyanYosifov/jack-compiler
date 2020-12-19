<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
use JackCompiler\CompileEngine\Compilations\ClassCompilation;

use function Tests\assertComplexCompiledData;

it('compiles a class declaration', function () {
    $classImplementation = 'class JackClass { }';
    $tokenizedData = Tokenizer::create()->handleStringData($classImplementation);
    $compiledData = ClassCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::START_CLASS()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'class');
    assertComplexCompiledData($compiledData, 1, CompilationType::IDENTIFIER(), 'JackClass');
    assertComplexCompiledData($compiledData, 2, CompilationType::SYMBOL(), '{');
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), '}');

    $this->assertNull($compiledData->getDataFrom(4));
});

it('compiles the classVarDec', function () {
    $classImplementation = 'class JackClass { field int dataScience; static char test; }';
    $tokenizedData = Tokenizer::create()->handleStringData($classImplementation);
    $compiledData = ClassCompilation::create()->compile($tokenizedData);

    $this->assertNotNull($classVarDecCompiledData = $compiledData->getDataFrom(3));
    $this->assertNotNull($classVarDecCompiledData2 = $compiledData->getDataFrom(4));
    $this->assertTrue(CompilationType::CLASS_VAR_DEC()->equals($classVarDecCompiledData->getType()));
    $this->assertTrue(CompilationType::CLASS_VAR_DEC()->equals($classVarDecCompiledData2->getType()));
});

it('throws a syntax error', function (string $implementation) {
    $this->expectException(InvalidSyntaxError::class);
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    ClassCompilation::create()->compile($tokenizedData);
})
    ->with([
        'class {}',
        'hacker JackClass { }',
    ]);
