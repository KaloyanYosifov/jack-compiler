<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;
use JackCompiler\CompileEngine\Compilations\Statements\StatementsCompilation;

class SubroutineBodyCompilation extends AbstractCompilation
{
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '{');

        while ($currentToken = $this->getCurrentToken()) {
            // if the current token is not the ending curly bracket
            // we start compiling other stuff
            if ($currentToken->getValue() === 'var') {
                $this->add(VarDecCompilation::create()->compile($tokenizedData));

                continue;
            }

            break;
        }

        $this->add(StatementsCompilation::create()->compile($tokenizedData));
        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '}');

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::SUBROUTINE_BODY();
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }
}
