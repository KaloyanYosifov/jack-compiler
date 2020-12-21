<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
use JackCompiler\CompileEngine\Compilations\Statements\WhileStatementCompilation;

use function Tests\assertComplexCompiledData;

it('compiles a while statement', function () {
    $implementation = <<<EOF
        while (test) {
        }
    EOF;

    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = WhileStatementCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::WHILE_STATEMENT()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'while');
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 2, CompilationType::EXPRESSION());
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), ')');
    assertComplexCompiledData($compiledData, 4, CompilationType::SYMBOL(), '{');
    assertComplexCompiledData($compiledData, 5, CompilationType::SYMBOL(), '}');

    $this->assertNull($compiledData->getDataFrom(6));
});

it('compiles a while statement with nested statements', function () {
    $implementation = <<<EOF
        while (test) {
            let testing = "hacker";
        }
    EOF;

    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = WhileStatementCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::WHILE_STATEMENT()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'while');
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 2, CompilationType::EXPRESSION());
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), ')');
    assertComplexCompiledData($compiledData, 4, CompilationType::SYMBOL(), '{');
    assertComplexCompiledData($compiledData, 5, CompilationType::STATEMENTS());
    assertComplexCompiledData($compiledData, 6, CompilationType::SYMBOL(), '}');

    $this->assertNull($compiledData->getDataFrom(7));
});

it('throws a syntax error', function (string $implementation) {
    $this->expectException(InvalidSyntaxError::class);
    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    WhileStatementCompilation::create()->compile($tokenizedData);
})
    ->with([
        'while () {}',
        'while ( {}',
        'while {}',
        'while test {}',
        'while (test) {df a}',
        'while (test) {',
        'while (test) }',
    ]);
