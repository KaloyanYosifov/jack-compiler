<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;

class ExpressionCompilation extends AbstractCompilation
{
    protected string $operands = '+|-|*|/|&|\||<|>|=';

    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $this->add(TermCompilation::create()->compile($tokenizedData));

        $currentToken = $this->getCurrentToken();

        if (
            $currentToken &&
            !in_array($currentToken->getValue(), [']', ')']) &&
            $currentToken->getType()->equals(TokenType::SYMBOL())
        ) {
            $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), $this->operands);
            $this->add(TermCompilation::create()->compile($tokenizedData));
        }

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::EXPRESSION();
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }
}
