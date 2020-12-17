<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\CompileEngine\CompiledData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;

interface Compilation
{
    /**
     * @return ComplexCompiledData|CompiledData
     */
    public function compile();

    public function getCompilationType(): CompilationType;
}
