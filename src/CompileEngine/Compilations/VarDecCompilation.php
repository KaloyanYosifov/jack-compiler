<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;

class VarDecCompilation extends AbstractCompilation
{
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), 'var');

        // since variables compilation cannot work alone we add their compiled data to this one
        $complexCompiledData = VariablesCompilation::create()->compile($tokenizedData);

        foreach ($complexCompiledData as $compiledData) {
            $this->add($compiledData);
        }

        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), ';');

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::VAR_DEC();
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }
}
