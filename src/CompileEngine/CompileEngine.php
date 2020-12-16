<?php

namespace JackCompiler\CompileEngine;

use JackCompiler\Tokenizer\TokenizedData;

class CompileEngine
{
    protected TokenizedData $tokenizedData;

    public function __construct(TokenizedData $tokenizedData)
    {
        $this->tokenizedData = $tokenizedData;
    }

    public function compile(): string
    {

    }
}
