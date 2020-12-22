<?php

namespace JackCompiler\CompileEngine\Compilations;

use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\CompileEngine\CompilationTokenExpector;

class ClassCompilation extends AbstractCompilation
{
    /**
     * @param TokenizedData $tokenizedData
     * @return ComplexCompiledData
     * @throws \JackCompiler\Exceptions\InvalidSyntaxError
     */
    public function compile(TokenizedData $tokenizedData): ComplexCompiledData
    {
        $this->init($tokenizedData, new ComplexCompiledData($this->getCompilationType()));

        $this->eat(CompilationType::KEYWORD(), TokenType::KEYWORD(), 'class');
        $this->eat(CompilationType::IDENTIFIER(), TokenType::IDENTIFIER());
        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '{');

        while ($currentToken = $this->getCurrentToken()) {
            // while we have a token
            // check if we have some of the decs
            if (in_array($currentToken->getValue(), ['field', 'static'])) {
                $this->add(ClassVarDecCompilation::create()->compile($tokenizedData));

                continue;
            }

            // while we have a token
            // check if we have some of the subroutine decs
            if (in_array($currentToken->getValue(), ['method', 'constructor', 'function'])) {
                $this->add(SubroutineDecCompilation::create()->compile($tokenizedData));

                continue;
            }

            break;
        }

        $this->eat(CompilationType::SYMBOL(), TokenType::SYMBOL(), '}');

        return $this->getComplexCompiledData();
    }

    public function getCompilationType(): CompilationType
    {
        return CompilationType::START_CLASS();
    }

    public static function create(): self
    {
        return new self(new CompilationTokenExpector());
    }
}
