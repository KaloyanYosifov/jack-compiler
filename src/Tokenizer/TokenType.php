<?php

namespace JackCompiler\Tokenizer;

use MyCLabs\Enum\Enum;

/**
 * @method static TokenType KEYWORD()
 * @method static TokenType SYMBOL()
 * @method static TokenType INTEGER()
 * @method static TokenType STRING()
 * @method static TokenType IDENTIFIER()
 * @extends Enum<string>
 */
class TokenType extends Enum
{
    private const KEYWORD = 'keyword';
    private const SYMBOL = 'symbol';
    private const INTEGER = 'integer';
    private const STRING = 'string';
    private const IDENTIFIER = 'identifier';
}
