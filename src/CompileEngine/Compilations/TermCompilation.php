<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Token;
use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;

class TermCompilation extends AbstractCompilation
{
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        /**
         * @var Token $currentToken
         */
        $currentToken = $this->getCurrentToken();

        if ($currentToken->getType()->equals(TokenType::STRING())) {
            $this->eat(CompilationType::STRING_CONSTANT(), TokenType::STRING());
        } elseif ($currentToken->getType()->equals(TokenType::INTEGER())) {
            $this->eat(CompilationType::INTEGER_CONSTANT(), TokenType::INTEGER());
        } elseif ($currentToken->getType()->equals(TokenType::KEYWORD())) {
            $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), 'true|false|null|this');
        } elseif ($currentToken->getType()->equals(TokenType::IDENTIFIER())) {
            $this->compileIdentifierLogic($tokenizedData);
        }

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::TERM();
    }

    private function compileIdentifierLogic(TokenizedData $tokenizedData): void
    {
        $this->eat(CompilationType::IDENTIFIER(), TokenType::IDENTIFIER());

        /**
         * @var Token $currentToken
         */
        $currentToken = $this->getCurrentToken();

        if ($currentToken->getValue() === '[') {
            $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '[');
            $this->add(ExpressionCompilation::create()->compile($tokenizedData));
            $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), ']');
        } elseif ($currentToken->getValue() === '(') {

        }
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }

}
