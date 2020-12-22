<?php

namespace JackCompiler\CompileEngine;

use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\Compilations\ClassCompilation;

class Compiler
{
    /**
     * @param TokenizedData $tokenizedData
     * @return ComplexCompiledData
     * @throws \JackCompiler\Exceptions\InvalidSyntaxError
     */
    public function handle(TokenizedData $tokenizedData): ComplexCompiledData
    {
        return ClassCompilation::create()->compile($tokenizedData);
    }
}
