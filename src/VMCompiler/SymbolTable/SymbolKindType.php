<?php

namespace JackCompiler\VMCompiler\SymbolTable;

use MyCLabs\Enum\Enum;

/**
 * @method static SymbolKindType LOCAL()
 * @method static SymbolKindType FIELD()
 * @method static SymbolKindType STATIC()
 * @method static SymbolKindType ARGUMENT()
 * @method static SymbolKindType INVALID()
 * @extends Enum<string>
 */
class SymbolKindType extends Enum
{
    private const LOCAL = 'local';
    private const FIELD = 'field';
    private const STATIC = 'static';
    private const ARGUMENT = 'argument';
    private const INVALID = 'invalid';
}
