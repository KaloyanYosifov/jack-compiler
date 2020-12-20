<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;

class ExpressionListCompilation extends AbstractCompilation
{
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $currentToken = $this->getCurrentToken();

        // if the next token is a closing bracket
        // just do nothing
        if (!$currentToken || $currentToken->getValue() === ')') {
            return $this->getComplexCompiledData();
        }

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::EXPRESSION_LIST();
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }
}
