<?php

namespace JackCompiler\VMCompiler\SymbolTable;

use JackCompiler\VMCompiler\SymbolTable\DataTransferObjects\SymbolData;

class SymbolTable
{
    protected ?self $parent;
    /**
     * @var SymbolData[]
     */
    protected array $symbols = [];

    /**
     * SymbolTable constructor.
     * @param self|null $parent
     */
    public function __construct(?self $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent(): self
    {
        return $this->parent;
    }

    public function addSymbol(string $name, string $type, SymbolKindType $symbolKindType): self
    {
        if (!array_key_exists($symbolKindType->getValue(), $this->symbols)) {
            $this->symbols[$symbolKindType->getValue()] = [];
        }

        $this->symbols[$symbolKindType->getValue()] = SymbolData::immutable([
            'name' => $name,
            'type' => $type,
            'kind' => $symbolKindType,
            // we require the index not the actual count
            'position' => count($this->symbols[$symbolKindType->getValue()]) - 1,
        ]);

        return $this;
    }

    /**
     * @param string $name
     * @return SymbolData
     */
    public function findByName(string $name)
    {
        foreach ($this->symbols as $kind => $symbolData) {
            if ($symbolData->name === $name) {
                return $symbolData;
            }
        }

        return SymbolData::emptyObject();
    }
}
