<?php

namespace JackCompiler\Tokenizer;

use JackCompiler\Exceptions\InvalidIdentifierName;

class TokensMap
{
    protected array $keywords = [
        'class', 'constructor', 'function', 'method',
        'field', 'static', 'var', 'int', 'char', 'boolean',
        'void', 'true', 'false', 'null', 'this', 'let', 'do',
        'if', 'else', 'while', 'return',
    ];
    protected array $symbols = [
        '{', '}', '(', ')', '[', ']', '. ', ', ', '; ', '+', '-', '*',
        '/', '&', ',', '<', '>', '=', '~',
    ];
    protected string $alphabet = 'abcdefghijklmnopqrstuvwxyz';

    public function getTokenType(string $token): TokenType
    {
        if (in_array($token, $this->keywords, true)) {
            return TokenType::KEYWORD();
        }

        if (in_array($token, $this->symbols, true)) {
            return TokenType::SYMBOL();
        }
        if (preg_match('~^".*?"$~', $token)) {
            return TokenType::STRING();
        }

        if (is_numeric($token)) {
            return TokenType::INTEGER();
        }

        if (stripos($this->alphabet, $token[0]) === false) {
            throw new InvalidIdentifierName($token);
        }

        return TokenType::IDENTIFIER();
    }
}
