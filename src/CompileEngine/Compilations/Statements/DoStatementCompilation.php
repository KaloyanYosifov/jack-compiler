<?php

namespace JackCompiler\CompileEngine\Compilations\Statements;

use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;
use JackCompiler\CompileEngine\Compilations\AbstractCompilation;
use JackCompiler\CompileEngine\Compilations\SubroutineCallCompilation;

class DoStatementCompilation extends AbstractCompilation
{
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), 'do');
        $this->add(SubroutineCallCompilation::create()->compile($tokenizedData));

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::DO_STATEMENT();
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }

    private function compileStatements(TokenizedData $tokenizedData): void
    {
        $currentToken = $this->getCurrentToken();

        // add statements compilation only if the current token is not the closing bracket for the if
        if ($currentToken && $currentToken->getValue() !== '}') {
            $this->add(StatementsCompilation::create()->compile($tokenizedData));
        }
    }
}
