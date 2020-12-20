<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
use JackCompiler\CompileEngine\Compilations\SubroutineDecCompilation;


use function Tests\assertComplexCompiledData;

it('compiles a subroutine dec', function () {
    $subroutineImplementation = <<<EOD
        method void testing() {
            
        }
    EOD;
    $tokenizedData = Tokenizer::create()->handleStringData($subroutineImplementation);
    $compiledData = SubroutineDecCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::SUBROUTINE_DEC()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'method');
    assertComplexCompiledData($compiledData, 1, CompilationType::KEYWORD(), 'void');
    assertComplexCompiledData($compiledData, 2, CompilationType::IDENTIFIER(), 'testing');
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 4, CompilationType::SYMBOL(), ')');
    assertComplexCompiledData($compiledData, 5, CompilationType::SYMBOL(), '{');
    assertComplexCompiledData($compiledData, 6, CompilationType::SYMBOL(), '}');
});

it('compiles a subroutine dec with parameter list', function () {
    $subroutineImplementation = <<<EOD
        method void testing(int hacker, JackClass test, char test) {
            
        }
    EOD;
    $tokenizedData = Tokenizer::create()->handleStringData($subroutineImplementation);
    $compiledData = SubroutineDecCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::SUBROUTINE_DEC()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'method');
    assertComplexCompiledData($compiledData, 1, CompilationType::KEYWORD(), 'void');
    assertComplexCompiledData($compiledData, 2, CompilationType::IDENTIFIER(), 'testing');
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 4, CompilationType::PARAMETER_LIST());
    assertComplexCompiledData($compiledData, 5, CompilationType::SYMBOL(), ')');
});

it('throws a syntax error', function (string $implementation) {
    $this->expectException(InvalidSyntaxError::class);
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    SubroutineDecCompilation::create()->compile($tokenizedData);
})
    ->with([
        'method void testing {}',
        'method tester () {}',
        'testing int thetest () {}',
    ]);
