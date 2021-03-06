<?php

namespace JackCompiler\CompileEngine\Compilations\Statements;

use JackCompiler\Tokenizer\Token;
use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;
use JackCompiler\CompileEngine\Compilations\AbstractCompilation;
use JackCompiler\CompileEngine\Compilations\ExpressionCompilation;

class IfStatementCompilation extends AbstractCompilation
{
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), 'if');
        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '(');
        $this->add(ExpressionCompilation::create()->compile($tokenizedData));
        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), ')');
        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '{');

        $this->compileStatements($tokenizedData);

        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '}');

        $currentToken = $this->getCurrentToken();
        // we have an else statement here if we have the else keyword
        if ($currentToken && $currentToken->getValue() === 'else') {
            $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), 'else');
            $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '{');

            $this->compileStatements($tokenizedData);

            $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '}');
        }

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::IF_STATEMENT();
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
