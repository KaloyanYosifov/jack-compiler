<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenizedData;

class ClassCompilation
{
    /**
     * @var TokenizedData
     */
    protected TokenizedData $tokenizedData;

    public function __construct(TokenizedData $tokenizedData)
    {
        $this->tokenizedData = $tokenizedData;
    }

    public function compile()
    {

    }
}
