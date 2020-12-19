<?php

namespace JackCompiler\CompileEngine;

use JackCompiler\Tokenizer\Token;
use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;
use JackCompiler\Exceptions\InvalidSyntaxError;

class CompilationTokenExpector
{
    /**
     * @param TokenizedData $tokenizedData
     * @param TokenType $tokenType
     * @param string $expectedValue
     * @return string
     * @throws InvalidSyntaxError
     */
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

        if ($expectedValue) {
            // we can check multiple values like "field|static"
            // so we need to create an array to accept that
            $expectedValues = [$expectedValue];
            if (strlen($expectedValue) > 1 && (strpos($expectedValue, '|') !== false)) {
                $expectedValues = explode('|', $expectedValue);
            }

            if (!in_array($currentToken->getValue(), $expectedValues)) {
                throw new InvalidSyntaxError('Expected: ' . $expectedValue . ' got :' . $currentToken->getValue());
            }
        }

        return $currentToken->getValue();
    }
}
