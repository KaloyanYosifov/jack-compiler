<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\Compilations\ClassCompilation;

it('compiles a class declaration', function () {
    $classImplementation = 'class JackClass { }';
    $tokenizedData = Tokenizer::create()->handleStringData($classImplementation);
    $compiledData = ClassCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::START_CLASS()->equals($compiledData->getType()));

    $this->assertTrue(CompilationType::KEYWORD()->equals($compiledData->getDataFrom(0)->getType()));
    $this->assertSame('class', $compiledData->getDataFrom(0)->getValue());

    $this->assertTrue(CompilationType::IDENTIFIER()->equals($compiledData->getDataFrom(1)->getType()));
    $this->assertSame('JackClass', $compiledData->getDataFrom(1)->getValue());

    $this->assertTrue(CompilationType::SYMBOL()->equals($compiledData->getDataFrom(2)->getType()));
    $this->assertSame('{', $compiledData->getDataFrom(2)->getValue());

    $this->assertTrue(CompilationType::SYMBOL()->equals($compiledData->getDataFrom(3)->getType()));
    $this->assertSame('}', $compiledData->getDataFrom(3)->getValue());

    $this->assertNull($compiledData->getDataFrom(4));
});

it('compiles the classVarDec', function () {
    $classImplementation = 'class JackClass { field int dataScience, kloshar; }';
    $tokenizedData = Tokenizer::create()->handleStringData($classImplementation);
    $compiledData = ClassCompilation::create()->compile($tokenizedData);

    $this->assertNotNull($classVarDecCompiledData = $compiledData->getDataFrom(3));
    $this->assertTrue(CompilationType::CLASS_VAR_DEC()->equals($classVarDecCompiledData->getType()));
});
