<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;

class ParameterListCompilation extends AbstractCompilation
{
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $parametersCompilation = VariablesCompilation::create(true)->compile($tokenizedData);

        foreach ($parametersCompilation as $parameterData) {
            $this->add($parameterData);
        }

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::PARAMETER_LIST();
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }
}
