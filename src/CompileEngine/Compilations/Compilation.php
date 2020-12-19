<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;

interface Compilation
{
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData;

    public function getCompilationType(): CompilationType;
}
