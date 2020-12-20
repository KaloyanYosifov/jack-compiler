<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
use JackCompiler\CompileEngine\Compilations\TermCompilation;

use function Tests\assertComplexCompiledData;

it('compiles a string term', function () {
    $implementation = '"test"';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = TermCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::TERM()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::STRING_CONSTANT(), '"test"');
});

it('compiles an integer term', function () {
    $implementation = '3321';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = TermCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::TERM()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::INTEGER_CONSTANT(), '3321');
});

it('compiles a keyword term', function (string $keyword) {
    $tokenizedData = Tokenizer::create()->handleStringData($keyword);
    $compiledData = TermCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::TERM()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), $keyword);
})
    ->with([
        'true',
        'false',
        'null',
        'this',
    ]);

it('compiles a identifier term', function () {
    $implementation = 'test';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = TermCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::TERM()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::IDENTIFIER(), 'test');
});

it('compiles an array term', function () {
    $implementation = 'test[32]';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = TermCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::TERM()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::IDENTIFIER(), 'test');
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), '[');
    assertComplexCompiledData($compiledData, 2, CompilationType::EXPRESSION());
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), ']');
});

it('compiles a subroutine call term', function () {
    $implementation = 'test()';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = TermCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::TERM()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::IDENTIFIER(), 'test');
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 2, CompilationType::EXPRESSION_LIST());
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), ')');
});

it('compiles a class subroutine call term', function () {
    $implementation = 'JackClass.test()';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = TermCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::TERM()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::IDENTIFIER(), 'JackClass');
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), '.');
    assertComplexCompiledData($compiledData, 2, CompilationType::IDENTIFIER());
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 4, CompilationType::EXPRESSION_LIST());
    assertComplexCompiledData($compiledData, 5, CompilationType::SYMBOL(), ')');
});

it('compiles a unary op term', function () {
    $implementation = '~test';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = TermCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::TERM()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::SYMBOL(), '~');
    assertComplexCompiledData($compiledData, 1, CompilationType::TERM());
});

it('compiles an expression term', function () {
    $implementation = '(test = hello)';
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = TermCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::TERM()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 1, CompilationType::EXPRESSION());
    assertComplexCompiledData($compiledData, 2, CompilationType::SYMBOL(), ')');
});

it('throws a syntax error', function (string $implementation) {
    $this->expectException(InvalidSyntaxError::class);
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    TermCompilation::create()->compile($tokenizedData);
})
    ->with([
        'test[24',
    ]);
