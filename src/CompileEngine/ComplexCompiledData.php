<?php

namespace JackCompiler\CompileEngine;

class ComplexCompiledData
{
    protected CompilationType $type;
    protected array $data = [];

    /**
     * ComplexCompiledData constructor.
     * @param CompilationType $type
     */
    public function __construct(CompilationType $type)
    {
        $this->type = $type;
    }

    /**
     * @param ComplexCompiledData|CompiledData $compilationData
     * @return self
     */
    public function add($compilationData): self
    {
        $this->data[] = $compilationData;

        return $this;
    }

    public function getType(): CompilationType
    {
        return $this->type;
    }
}
