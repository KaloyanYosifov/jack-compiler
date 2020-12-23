<?php

namespace JackCompiler\Tokenizer;

class TokenizedData
{
    protected $tokens = [];
    protected $currentTokenIndex = 0;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    public function reset(): self
    {
        $this->currentTokenIndex = 0;

        return $this;
    }

    public function nextToken(): self
    {
        $this->currentTokenIndex++;

        return $this;
    }

    public function currentToken(): ?Token
    {
        return $this->tokens[$this->currentTokenIndex] ?? null;
    }

    public function peekNextToken(): ?Token
    {
        return $this->tokens[$this->currentTokenIndex + 1] ?? null;
    }

    public function hasMoreTokens(): bool
    {
        return $this->currentTokenIndex < count($this->tokens);
    }
}
