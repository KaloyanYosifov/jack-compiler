<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
use JackCompiler\CompileEngine\Compilations\VarDecCompilation;

use function Tests\assertComplexCompiledData;

it('compiles a var declaration', function () {
    $classImplementation = 'var int helloThere; var char testing, hackTest;';
    $tokenizedData = Tokenizer::create()->handleStringData($classImplementation);
    $compiledData = VarDecCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::VAR_DEC()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'var');
    assertComplexCompiledData($compiledData, 1, CompilationType::KEYWORD(), 'int');
    assertComplexCompiledData($compiledData, 2, CompilationType::IDENTIFIER(), 'helloThere');
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), ';');

    $nextClassVarCompiledData = VarDecCompilation::create()->compile($tokenizedData);

    assertComplexCompiledData($nextClassVarCompiledData, 0, CompilationType::KEYWORD(), 'var');
    assertComplexCompiledData($nextClassVarCompiledData, 1, CompilationType::KEYWORD(), 'char');
    assertComplexCompiledData($nextClassVarCompiledData, 2, CompilationType::IDENTIFIER(), 'testing');
    assertComplexCompiledData($nextClassVarCompiledData, 3, CompilationType::SYMBOL(), ',');
    assertComplexCompiledData($nextClassVarCompiledData, 4, CompilationType::IDENTIFIER(), 'hackTest');
    assertComplexCompiledData($nextClassVarCompiledData, 5, CompilationType::SYMBOL(), ';');
});

it('throws a syntax error', function (string $implementation) {
    $this->expectException(InvalidSyntaxError::class);
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    VarDecCompilation::create()->compile($tokenizedData);
})
    ->with([
        'var helloThere;',
        'var;',
        'var int test, var test;',
    ]);
