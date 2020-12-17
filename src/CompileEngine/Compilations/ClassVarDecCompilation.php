<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;

class ClassVarDecCompilation implements Compilation
{
    /**
     * @var TokenizedData
     */
    protected TokenizedData $tokenizedData;

    public function __construct(TokenizedData $tokenizedData)
    {
        $this->tokenizedData = $tokenizedData;
    }

    public function compile(): ComplexCompiledData
    {
        $complexCompiledData = new ComplexCompiledData($this->getCompilationType());

        return $complexCompiledData;
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::CLASS_VAR_DEC();
    }
}
