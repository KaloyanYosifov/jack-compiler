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
    protected ?TokenizedData $currentTokenizedData = null;
    protected ?ComplexCompiledData $currentComplexCompiledData = null;
    protected CompilationTokenExpector $compilationTokenExpector;

    public function __construct(CompilationTokenExpector $compilationTokenExpector)
    {
        $this->compilationTokenExpector = $compilationTokenExpector;
    }

    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->currentComplexCompiledData = new ComplexCompiledData($this->getCompilationType());
        $this->currentTokenizedData = $tokenizedData;

        $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), 'class');
        $this->eat(CompilationType::IDENTIFIER(), TokenType::IDENTIFIER());
        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '{');
        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '}');

        return $this->currentComplexCompiledData;
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::START_CLASS();
    }

    private function eat(CompilationType $compilationType, TokenType $tokenType, string $expectedValue = ''): self
    {
        if (!$this->currentComplexCompiledData || !$this->currentTokenizedData) {
            throw new \LogicException('This method should be called after initializing tokenized data and complex compiled data!');
        }

        $this->currentComplexCompiledData->add(
            new CompiledData(
                $compilationType,
                $this->compilationTokenExpector->expect($this->currentTokenizedData, $tokenType, $expectedValue)
            )
        );
        $this->currentTokenizedData->nextToken();

        return $this;
    }

    public function __destruct()
    {
        $this->currentTokenizedData = null;
        $this->currentComplexCompiledData = null;
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }
}
