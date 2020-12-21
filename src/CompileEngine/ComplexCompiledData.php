<?php

namespace JackCompiler\CompileEngine;

/**
 * Class ComplexCompiledData
 * @package JackCompiler\CompileEngine
 *
 * @implements \IteratorAggregate<int, ComplexCompiledData|CompiledData>
 */
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

    public function isEmpty(): bool
    {
        return count($this->data) <= 0;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
}
