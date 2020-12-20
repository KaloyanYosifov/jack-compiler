<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
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

        $this->add(ExpressionCompilation::create()->compile($tokenizedData));
        $this->addMoreExpressions($tokenizedData);

        $currentToken = $this->getCurrentToken();

        // if the next token is a closing bracket
        // just do nothing
        if ($currentToken && $currentToken->getValue() !== ')') {
            throw new InvalidSyntaxError('When an expression list ends it must be closed with a closing ")" bracket!');
        }

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::EXPRESSION_LIST();
    }

    protected function addMoreExpressions(TokenizedData $tokenizedData): void
    {
        $currentToken = $this->getCurrentToken();

        // check if we have a comma after the previous expression
        // if we do we add another expression
        if (!$currentToken || $currentToken->getValue() !== ',') {
            return;
        }

        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), ',');
        $this->add(ExpressionCompilation::create()->compile($tokenizedData));

        $this->addMoreExpressions($tokenizedData);
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }
}
