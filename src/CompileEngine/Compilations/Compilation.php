<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompiledData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;

interface Compilation
{
    /**
     * @return ComplexCompiledData|CompiledData
     */
    public function compile(TokenizedData $tokenizedData);

    public function getCompilationType(): CompilationType;
}
