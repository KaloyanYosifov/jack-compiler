<?php

namespace JackCompiler\VMCompiler;

class SymbolTable
{
    protected ?SymbolTable $parent;
    protected array $symbols = [];

    /**
     * SymbolTable constructor.
     * @param SymbolTable|null $parent
     */
    public function __construct(?SymbolTable $parent = null)
    {
        $this->parent = $parent;
    }

}
