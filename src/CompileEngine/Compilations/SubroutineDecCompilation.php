<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;
use JackCompiler\CompileEngine\Compilations\Constants\CompilationConstants;

class SubroutineDecCompilation extends AbstractCompilation
{
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), 'method|constructor|function');
        $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), CompilationConstants::VAR_TYPES);
        $this->eat(CompilationType::IDENTIFIER(), TokenType::IDENTIFIER());

        $this->initMoreDecs();

        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), ';');

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::SUBROUTINE_DEC();
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }

    protected function initMoreDecs(): void
    {
        try {
            $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), ',');
        } catch (InvalidSyntaxError $exception) {
            // we skip the syntax error as it is just a check if we have more fields to define
            return;
        }

        $this->eat(CompilationType::IDENTIFIER(), TokenType::IDENTIFIER());

        $this->initMoreDecs();
    }
}
