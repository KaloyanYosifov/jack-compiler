<?php

namespace JackCompiler\CompileEngine;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\Compilations\ClassCompilation;

class Compiler
{
    public function handle(Tokenizer $tokenizer, string $filename): ComplexCompiledData
    {
        return ClassCompilation::create()->compile($tokenizer->handle($filename));
    }
}
