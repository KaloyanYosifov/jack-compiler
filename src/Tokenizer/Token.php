<?php

namespace JackCompiler\Tokenizer;

class Token
{
    protected $tokenType;
    protected $value;
    protected $line;
    protected $filename;

    /**
     * Token constructor.
     * @param TokenType $tokenType
     * @param string $value
     */
    public function __construct(
        TokenType $tokenType,
        string $value,
        int $line,
        string $filename
    ) {
        $this->tokenType = $tokenType;
        $this->value = $value;
        $this->line = $line;
        $this->filename = $filename;
    }

    public function getType(): TokenType
    {
        return $this->tokenType;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
