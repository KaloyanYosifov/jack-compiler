<?php

namespace JackCompiler\Tokenizer;

class Token
{
    protected TokenType $tokenType;
    protected string $value;

    /**
     * Token constructor.
     * @param TokenType $tokenType
     * @param string $value
     */
    public function __construct(TokenType $tokenType, string $value)
    {
        $this->tokenType = $tokenType;
        $this->value = $value;
    }

    public function getType(): TokenType
    {
        return $this->tokenType;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
