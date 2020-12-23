<?php

namespace JackCompiler\Tokenizer;

use JackCompiler\Exceptions\InvalidIdentifierName;

class TokensMap
{
    protected $keywords = [
        'class', 'constructor', 'function', 'method',
        'field', 'static', 'var', 'int', 'char', 'boolean',
        'void', 'true', 'false', 'null', 'this', 'let', 'do',
        'if', 'else', 'while', 'return',
    ];
    protected $symbols = [
        '{', '}', '(', ')', '[', ']', '.', ',', ';', '+', '-', '*',
        '/', '&', ',', '<', '>', '=', '~', '|',
    ];
    protected $allowedStartCharacters = 'abcdefghijklmnopqrstuvwxyz_';
    protected $allowedIdentifierCharacters = '';

    public function __construct()
    {
        $this->allowedIdentifierCharacters = $this->allowedStartCharacters . '0123456789';
    }

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

        if (stripos($this->allowedStartCharacters, $token[0]) === false) {
            throw new InvalidIdentifierName($token);
        }

        for ($i = 1, $tokenLength = strlen($token); $i < $tokenLength; $i++) {
            $tokenChar = $token[$i];

            if (stripos($this->allowedIdentifierCharacters, $tokenChar) === false) {
                throw new InvalidIdentifierName($token);
            }
        }

        return TokenType::IDENTIFIER();
    }

    public function isSymbol(string $char): bool
    {
        return in_array($char, $this->symbols, true);
    }
}
