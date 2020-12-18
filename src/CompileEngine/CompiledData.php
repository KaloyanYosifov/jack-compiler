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

    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): CompilationType
    {
        return $this->type;
    }
}
