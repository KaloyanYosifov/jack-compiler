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
        'let' => 'letStatement',
        'if' => 'ifStatement',
        'while' => 'whileStatement',
        'do' => 'doStatement',
        'return' => 'returnStatement',
    ];

    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

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
