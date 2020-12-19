<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\Exceptions\InvalidSyntaxError;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;
use JackCompiler\CompileEngine\Compilations\Constants\CompilationConstants;

class VariablesCompilation extends AbstractCompilation
{
    protected bool $typeForEveryNewVariable;

    /**
     * VariablesCompilation constructor.
     * @param CompilationTokenExpector $compilationTokenExpector
     * @param bool $typeForEveryNewVariable
     */
    public function __construct(CompilationTokenExpector $compilationTokenExpector, bool $typeForEveryNewVariable)
    {
        parent::__construct($compilationTokenExpector);

        $this->typeForEveryNewVariable = $typeForEveryNewVariable;
    }

    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), CompilationConstants::VAR_TYPES);
        $this->eat(CompilationType::IDENTIFIER(), TokenType::IDENTIFIER());

        $this->initMoreFields();

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::VARS();
    }

    public static function create(bool $typeForEveryNewVariable = false): self
    {
        return new self(new CompilationTokenExpector(), $typeForEveryNewVariable);
    }

    protected function initMoreFields(): void
    {
        try {
            $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), ',');
        } catch (InvalidSyntaxError $exception) {
            // we skip the syntax error as it is just a check if we have more fields to define
            return;
        }

        if ($this->typeForEveryNewVariable) {
            $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), CompilationConstants::VAR_TYPES);
        }

        $this->eat(CompilationType::IDENTIFIER(), TokenType::IDENTIFIER());

        $this->initMoreFields();
    }
}
