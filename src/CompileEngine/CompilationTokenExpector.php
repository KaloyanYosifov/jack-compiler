<?php

namespace JackCompiler\CompileEngine;

use JackCompiler\Tokenizer\Token;
use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\Exceptions\InvalidSyntaxError;

class CompilationTokenExpector
{
    public function expect(TokenizedData $tokenizedData, TokenType $tokenType, string $expectedValue = ''): string
    {
        if (!$tokenizedData->hasMoreTokens()) {
            throw new InvalidSyntaxError('Reached end of file with more for parsing left!');
        }

        /**
         * @var Token $currentToken
         */
        $currentToken = $tokenizedData->currentToken();

        if (!$currentToken->getType()->equals($tokenType)) {
            throw new InvalidSyntaxError('Expected: ' . $tokenType->getValue() . ' got :' . $currentToken->getType()->getValue());
        }

        if ($expectedValue && $currentToken->getValue() !== $expectedValue) {
            throw new InvalidSyntaxError('Expected: ' . $expectedValue . ' got :' . $currentToken->getValue());
        }

        return $currentToken->getValue();
    }
}
