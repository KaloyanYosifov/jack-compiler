<?php

namespace JackCompiler\VMCompiler\SymbolTable\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use JackCompiler\VMCompiler\SymbolTable\SymbolKindType;

class SymbolData extends DataTransferObject
{
    public string $name;
    public string $type;
    public SymbolKindType $kind;
    public int $position;

    public static function emptyObject(): self
    {
        return new self([
            'name' => '',
            'type' => '',
            'kind' => SymbolKindType::INVALID(),
            'position' => -1,
        ]);
    }
}
