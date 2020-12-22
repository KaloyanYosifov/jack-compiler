<?php

namespace JackCompiler\CompileEngine;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\Compilations\ClassCompilation;

class Compiler
{
    /**
     * @param Tokenizer $tokenizer
     * @param string $filename
     * @return ComplexCompiledData
     * @throws \JackCompiler\Exceptions\InvalidSyntaxError
     */
    public function handle(Tokenizer $tokenizer, string $filename): ComplexCompiledData
    {
        return ClassCompilation::create()->compile($tokenizer->handle($filename));
    }
}
