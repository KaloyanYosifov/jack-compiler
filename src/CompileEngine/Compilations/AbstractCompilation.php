<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Token;
use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompiledData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;

abstract class AbstractCompilation implements Compilation
{
    private ?TokenizedData $currentTokenizedData = null;
    private ?ComplexCompiledData $currentComplexCompiledData = null;
    private CompilationTokenExpector $compilationTokenExpector;

    public function __construct(CompilationTokenExpector $compilationTokenExpector)
    {
        $this->compilationTokenExpector = $compilationTokenExpector;
    }

    abstract public function compile(TokenizedData $tokenizedData): ComplexCompiledData;

    abstract public function getCompilationType(): CompilationType;

    protected function init(TokenizedData $tokenizedData, ComplexCompiledData $complexCompiledData): void
    {
        $this->currentTokenizedData = $tokenizedData;
        $this->currentComplexCompiledData = $complexCompiledData;
    }

    /**
     * @param CompilationType $compilationType
     * @param TokenType $tokenType
     * @param string $expectedValue
     * @return $this
     * @throws \JackCompiler\Exceptions\InvalidSyntaxError
     */
    protected function eat(CompilationType $compilationType, TokenType $tokenType, string $expectedValue = ''): self
    {
        if (!$this->currentComplexCompiledData || !$this->currentTokenizedData) {
            throw new \LogicException('This method should be called after initializing tokenized data and complex compiled data!');
        }

        $this->add(
            new CompiledData(
                $compilationType,
                $this->compilationTokenExpector->expect($this->currentTokenizedData, $tokenType, $expectedValue)
            )
        );
        $this->currentTokenizedData->nextToken();

        return $this;
    }

    /**
     * @param ComplexCompiledData|CompiledData $compiledData
     * @return $this
     */
    protected function add($compiledData): self
    {
        if (!$this->currentComplexCompiledData || !$this->currentTokenizedData) {
            throw new \LogicException('This method should be called after initializing tokenized data and complex compiled data!');
        }

        $this->currentComplexCompiledData->add($compiledData);

        return $this;
    }

    protected function peekNextToken(): ?Token
    {
        if (!$this->currentTokenizedData) {
            throw new \LogicException('This method should be called after initializing tokenized data and complex compiled data!');
        }

        return $this->currentTokenizedData->currentToken();
    }

    protected function getCurrentToken(): ?Token
    {
        if (!$this->currentTokenizedData) {
            throw new \LogicException('This method should be called after initializing tokenized data and complex compiled data!');
        }

        return $this->currentTokenizedData->currentToken();
    }

    protected function getComplexCompiledData(): ComplexCompiledData
    {
        if (!$this->currentComplexCompiledData) {
            throw new \LogicException('This method should be called after initializing tokenized data and complex compiled data!');
        }

        return clone $this->currentComplexCompiledData;
    }

    public function __destruct()
    {
        $this->currentTokenizedData = null;
        $this->currentComplexCompiledData = null;
    }
}
