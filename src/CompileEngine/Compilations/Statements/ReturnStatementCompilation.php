<?php

namespace JackCompiler\CompileEngine\Compilations\Statements;

use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;
use JackCompiler\CompileEngine\Compilations\AbstractCompilation;
use JackCompiler\CompileEngine\Compilations\ExpressionCompilation;

class ReturnStatementCompilation extends AbstractCompilation
{
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), 'return');

        $currentToken = $this->getCurrentToken();

        // we have an expression before returning
        if ($currentToken && $currentToken->getValue() !== ';') {
            $this->add(ExpressionCompilation::create()->compile($tokenizedData));
        }

        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), ';');

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::RETURN_STATEMENT();
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }
}
