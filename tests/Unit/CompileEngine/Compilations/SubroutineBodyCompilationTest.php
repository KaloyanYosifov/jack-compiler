<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
use JackCompiler\CompileEngine\Compilations\SubroutineBodyCompilation;

use function Tests\assertComplexCompiledData;

it('compiles a subroutine dec', function () {
    $subroutineImplementation = <<<EOD
        {}
    EOD;
    $tokenizedData = Tokenizer::create()->handleStringData($subroutineImplementation);
    $compiledData = SubroutineBodyCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::SUBROUTINE_BODY()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::SYMBOL(), '{');
    assertComplexCompiledData($compiledData, 1, CompilationType::STATEMENTS());
    assertComplexCompiledData($compiledData, 2, CompilationType::SYMBOL(), '}');
});

it('compiles a subroutine dec with var dec', function () {
    $subroutineImplementation = <<<EOD
        {
            var int testing;
            var char hello, world;
        }
    EOD;
    $tokenizedData = Tokenizer::create()->handleStringData($subroutineImplementation);
    $compiledData = SubroutineBodyCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::SUBROUTINE_BODY()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::SYMBOL(), '{');
    assertComplexCompiledData($compiledData, 1, CompilationType::VAR_DEC());
    assertComplexCompiledData($compiledData, 2, CompilationType::VAR_DEC());
    assertComplexCompiledData($compiledData, 3, CompilationType::STATEMENTS());
    assertComplexCompiledData($compiledData, 4, CompilationType::SYMBOL(), '}');
});

it('compiles a subroutine dec with statements', function () {
    $subroutineImplementation = <<<EOD
        {
            let helloWorld = "hello";
        }
    EOD;
    $tokenizedData = Tokenizer::create()->handleStringData($subroutineImplementation);
    $compiledData = SubroutineBodyCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::SUBROUTINE_BODY()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::SYMBOL(), '{');
    assertComplexCompiledData($compiledData, 1, CompilationType::STATEMENTS());
    assertComplexCompiledData($compiledData, 2, CompilationType::SYMBOL(), '}');
});

it('throws a syntax error', function (string $implementation) {
    $this->expectException(InvalidSyntaxError::class);
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    SubroutineBodyCompilation::create()->compile($tokenizedData);
})
    ->with([
        'var int test',
        '{ test }',
    ]);
