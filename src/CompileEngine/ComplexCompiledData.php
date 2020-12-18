<?php

namespace JackCompiler\CompileEngine;

class ComplexCompiledData implements \IteratorAggregate
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

    /**
     * @param int $index
     * @return ComplexCompiledData|CompiledData|null
     */
    public function getDataFrom(int $index)
    {
        if (empty($this->data[$index])) {
            return null;
        }

        return $this->data[$index];
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    public function offsetSet($offset, $value): void
    {
        throw new \LogicException('Overriding compile data is not allowed!');
    }

    public function offsetUnset($offset): void
    {
        throw new \LogicException('Overriding compile data is not allowed!');
    }
}
