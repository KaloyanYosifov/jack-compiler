<?php

namespace JackCompiler\CompileEngine\Compilations\Statements;

use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;
use JackCompiler\CompileEngine\Compilations\AbstractCompilation;

class StatementsCompilation extends AbstractCompilation
{
    protected array $statementTypes = [
        'let' => LetStatementCompilation::class,
        'if' => IfStatementCompilation::class,
        'while' => WhileStatementCompilation::class,
        'do' => DoStatementCompilation::class,
        'return' => ReturnStatementCompilation::class,
    ];

    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        while ($currentToken = $this->getCurrentToken()) {
            if (array_key_exists($currentToken->getValue(), $this->statementTypes)) {
                $statementClassToCompile = $this->statementTypes[$currentToken->getValue()]::create();

                $this->add($statementClassToCompile->compile($tokenizedData));
                continue;
            }

            break;
        }

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::STATEMENTS();
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }
}
