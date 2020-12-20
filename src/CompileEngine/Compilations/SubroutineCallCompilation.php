<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Token;
use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;

class SubroutineCallCompilation extends AbstractCompilation
{
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $this->eat(CompilationType::IDENTIFIER(), TokenType::IDENTIFIER());

        /**
         * @var Token $currentToken
         */
        $currentToken = $this->getCurrentToken();

        // we are compiling a class subroutine call
        if ($currentToken->getValue() === '.') {
            $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '.');
            $this->eat(CompilationType::IDENTIFIER(), TokenType::IDENTIFIER());
        }

        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '(');
        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), ')');

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::SUBROUTINE_CALL();
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }
}
