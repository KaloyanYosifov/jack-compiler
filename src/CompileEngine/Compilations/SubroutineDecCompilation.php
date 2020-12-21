<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;
use JackCompiler\CompileEngine\Compilations\Constants\CompilationConstants;

class SubroutineDecCompilation extends AbstractCompilation
{
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), 'method|constructor|function');
        $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), CompilationConstants::VAR_TYPES . '|void');
        $this->eat(CompilationType::IDENTIFIER(), TokenType::IDENTIFIER());
        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '(');
        $this->add(ParameterListCompilation::create()->compile($tokenizedData));
        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), ')');
        $this->add(SubroutineBodyCompilation::create()->compile($tokenizedData));

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
}
