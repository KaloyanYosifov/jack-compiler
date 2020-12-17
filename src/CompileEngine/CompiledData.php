<?php

namespace JackCompiler\CompileEngine;

class CompiledData
{
    protected CompilationType $type;
    protected string $value;

    public function __construct(CompilationType $type, string $value)
    {
        $this->type = $type;
        $this->value = $value;
    }
}
