<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
use JackCompiler\CompileEngine\Compilations\Statements\IfStatementCompilation;

use function Tests\assertComplexCompiledData;

it('compiles an if statement', function () {
    $implementation = <<<EOF
        if (test) {
        }
    EOF;

    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = IfStatementCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::IF_STATEMENT()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'if');
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 2, CompilationType::EXPRESSION());
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), ')');
    assertComplexCompiledData($compiledData, 4, CompilationType::SYMBOL(), '{');
    assertComplexCompiledData($compiledData, 5, CompilationType::SYMBOL(), '}');

    $this->assertNull($compiledData->getDataFrom(6));
});

it('compiles an if statement with an else', function () {
    $implementation = <<<EOF
        if (test) {
        } else {
        }
    EOF;

    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = IfStatementCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::IF_STATEMENT()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'if');
    assertComplexCompiledData($compiledData, 1, CompilationType::SYMBOL(), '(');
    assertComplexCompiledData($compiledData, 2, CompilationType::EXPRESSION());
    assertComplexCompiledData($compiledData, 3, CompilationType::SYMBOL(), ')');
    assertComplexCompiledData($compiledData, 4, CompilationType::SYMBOL(), '{');
    assertComplexCompiledData($compiledData, 5, CompilationType::SYMBOL(), '}');
    assertComplexCompiledData($compiledData, 6, CompilationType::KEYWORD(), 'else');
    assertComplexCompiledData($compiledData, 7, CompilationType::SYMBOL(), '{');
    assertComplexCompiledData($compiledData, 8, CompilationType::SYMBOL(), '}');

    $this->assertNull($compiledData->getDataFrom(9));
});

it('compiles an if statement with nested statements', function () {
    $implementation = <<<EOF
        if (test) {
            let testing = "hacker";
        }
    EOF;

    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = IfStatementCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::IF_STATEMENT()->equals($compiledData->getType()));

    assertComplexCompiledData($compiledData, 0, CompilationType::KEYWORD(), 'if');
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
    IfStatementCompilation::create()->compile($tokenizedData);
})
    ->with([
        'if () {}',
        'if ( {}',
        'if {}',
        'if test {}',
    ]);
