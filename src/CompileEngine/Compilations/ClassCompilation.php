<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompiledData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;

class ClassCompilation implements Compilation
{
    protected TokenizedData $tokenizedData;
    protected CompilationTokenExpector $compilationTokenExpector;

    public function __construct(TokenizedData $tokenizedData, CompilationTokenExpector $compilationTokenExpector)
    {
        $this->tokenizedData = $tokenizedData;
        $this->compilationTokenExpector = $compilationTokenExpector;
    }

    public function compile(): ComplexCompiledData
    {
        $complexCompiledData = new ComplexCompiledData($this->getCompilationType());
        $complexCompiledData->add(
            new CompiledData(
                CompilationType::KEYWORD(),
                $this->compilationTokenExpector->expect($this->tokenizedData, TokenType::KEYWORD(), 'class')
            )
        );
        $this->tokenizedData->nextToken();
        $complexCompiledData->add(
            new CompiledData(
                CompilationType::IDENTIFIER(),
                $this->compilationTokenExpector->expect($this->tokenizedData, TokenType::IDENTIFIER())
            )
        );
        $this->tokenizedData->nextToken();
        $complexCompiledData->add(
            new CompiledData(
                CompilationType::SYMBOL(),
                $this->compilationTokenExpector->expect($this->tokenizedData, TokenType::SYMBOL(), '{')
            )
        );
        $this->tokenizedData->nextToken();
        $complexCompiledData->add(
            new CompiledData(
                CompilationType::SYMBOL(),
                $this->compilationTokenExpector->expect($this->tokenizedData, TokenType::SYMBOL(), '}')
            )
        );
        $this->tokenizedData->nextToken();

        return $complexCompiledData;
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::START_CLASS();
    }

    public static function create(TokenizedData $tokenizedData): self
    {
        return new self($tokenizedData, new CompilationTokenExpector());
    }
}
