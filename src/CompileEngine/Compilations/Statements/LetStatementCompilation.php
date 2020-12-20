<?php

namespace JackCompiler\CompileEngine\Compilations\Statements;

use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;
use JackCompiler\CompileEngine\Compilations\AbstractCompilation;
use JackCompiler\CompileEngine\Compilations\ExpressionCompilation;

class LetStatementCompilation extends AbstractCompilation
{
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), 'let');
        $this->eat(CompilationType::IDENTIFIER(), TokenType::IDENTIFIER());
        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '=');
        $this->add(ExpressionCompilation::create()->compile($tokenizedData));

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::LET_STATEMENT();
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }
}
