<?php

namespace Tests\Unit\Tokenizer;

use JackCompiler\Tokenizer\Token;
use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;

it('shows if it has more tokens', function () {
    $token = new Token(TokenType::KEYWORD(), 'value', 0, 'test');
    $tokenizedData = new TokenizedData([$token]);

    expect($tokenizedData->hasMoreTokens())->toBeTrue();
    $tokenizedData->nextToken();
    expect($tokenizedData->hasMoreTokens())->toBeFalse();
});

it('resets to first token', function () {
    $tokens = [
        new Token(TokenType::KEYWORD(), 'value', 0, 'test'),
        new Token(TokenType::IDENTIFIER(), 'something', 0, 'test'),
    ];
    $tokenizedData = new TokenizedData($tokens);

    expect($tokenizedData->currentToken()->getType()->getValue())
        ->toBe(TokenType::KEYWORD()->getValue());

    $tokenizedData->nextToken();

    expect($tokenizedData->currentToken()->getType()->getValue())
        ->toBe(TokenType::IDENTIFIER()->getValue());

    $tokenizedData->reset();

    expect($tokenizedData->currentToken()->getType()->getValue())
        ->toBe(TokenType::KEYWORD()->getValue());
});

it('advances to next token', function () {
    $tokens = [
        new Token(TokenType::KEYWORD(), 'value', 0, 'test'),
        new Token(TokenType::IDENTIFIER(), 'something', 0, 'test'),
    ];
    $tokenizedData = new TokenizedData($tokens);

    expect($tokenizedData->currentToken()->getType()->getValue())
        ->toBe(TokenType::KEYWORD()->getValue());

    $tokenizedData->nextToken();

    expect($tokenizedData->currentToken()->getType()->getValue())
        ->toBe(TokenType::IDENTIFIER()->getValue());

    $tokenizedData->nextToken();

    expect($tokenizedData->currentToken())
        ->toBeNull();
});

it('can peek next token', function () {
    $tokens = [
        new Token(TokenType::KEYWORD(), 'value', 0, 'test'),
        new Token(TokenType::IDENTIFIER(), 'something', 0, 'test'),
    ];
    $tokenizedData = new TokenizedData($tokens);

    expect($tokenizedData->currentToken()->getType()->getValue())
        ->toBe(TokenType::KEYWORD()->getValue());

    expect($tokenizedData->peekNextToken()->getType()->getValue())
        ->toBe(TokenType::IDENTIFIER()->getValue());

    expect($tokenizedData->currentToken()->getType()->getValue())
        ->toBe(TokenType::KEYWORD()->getValue());
});

it('returns null if there is no token left to peek', function () {
    $tokens = [
        new Token(TokenType::KEYWORD(), 'value', 0, 'test'),
    ];
    $tokenizedData = new TokenizedData($tokens);

    expect($tokenizedData->peekNextToken())
        ->toBeNull();
});
