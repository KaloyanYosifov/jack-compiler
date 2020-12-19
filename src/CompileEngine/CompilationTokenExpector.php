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
        //we skip the token and value checks
        // if we have "className" in the expected value
        // and if we use a keyword
        // since the className is a special token that can be anything we do not check for types or values
        // the checks if we do have the class is going to happen at runtime
        $skipTokenAndValueChecks = str_contains($expectedValue, 'className') && $tokenType->equals(TokenType::KEYWORD());

        if (!$skipTokenAndValueChecks && !$currentToken->getType()->equals($tokenType)) {
            throw new InvalidSyntaxError('Expected: ' . $tokenType->getValue() . ' got :' . $currentToken->getType()->getValue());
        }

        // we check if we have an expected value and
        // we also check if we are using a keyword and the expect value contains className
        // if that is the case we skip the if condition as className can be any type
        if ($expectedValue && !$skipTokenAndValueChecks) {
            // we can check multiple values like "field|static"
            // so we need to create an array to accept that
            $expectedValues = [$expectedValue];
            if (strlen($expectedValue) > 1 && (strpos($expectedValue, '|') !== false)) {
                $expectedValues = explode('|', $expectedValue);
            }

            if (!in_array($currentToken->getValue(), $expectedValues)) {
                throw new InvalidSyntaxError('Expected "' . $expectedValue . '" got "' . $currentToken->getValue() . '"');
            }
        }

        return $currentToken->getValue();
    }
}
