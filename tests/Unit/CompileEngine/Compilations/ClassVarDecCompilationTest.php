<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
use JackCompiler\CompileEngine\Compilations\ClassVarDecCompilation;

use function Tests\assertComplexCompiledData;

it('compiles a class var declaration', function () {
    $classImplementation = 'field int helloThere; static char testing, hackTest;';
    $tokenizedData = Tokenizer::create()->handleStringData($classImplementation);
    $compiledData = ClassVarDecCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::CLASS_VAR_DEC()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'field');
    assertComplexCompiledData($compiledData, 1, CompilationType::KEYWORD(), 'int');
    assertComplexCompiledData($compiledData, 2, CompilationType::IDENTIFIER(), 'helloThere');
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), ';');

    $nextClassVarCompiledData = ClassVarDecCompilation::create()->compile($tokenizedData);

    assertComplexCompiledData($nextClassVarCompiledData, 0, CompilationType::KEYWORD(), 'static');
    assertComplexCompiledData($nextClassVarCompiledData, 1, CompilationType::KEYWORD(), 'char');
    assertComplexCompiledData($nextClassVarCompiledData, 2, CompilationType::IDENTIFIER(), 'testing');
    assertComplexCompiledData($nextClassVarCompiledData, 3, CompilationType::SYMBOL(), ',');
    assertComplexCompiledData($nextClassVarCompiledData, 4, CompilationType::IDENTIFIER(), 'hackTest');
    assertComplexCompiledData($nextClassVarCompiledData, 5, CompilationType::SYMBOL(), ';');
});

it('throws a syntax error', function (string $implementation) {
    $this->expectException(InvalidSyntaxError::class);
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    ClassVarDecCompilation::create()->compile($tokenizedData);
})
    ->with([
        'field helloThere;',
        'field helloThere fast;',
        'method helloThere fast;',
        'static;',
    ]);
