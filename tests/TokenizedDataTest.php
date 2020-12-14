<?php

namespace Tests\Unit\JackCompiler\Tokenizer;

use JackCompiler\Tokenizer\Token;
use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Tokenizer\TokenizedData;

it('shows if it has more tokens', function () {
    $token = new Token(TokenType::KEYWORD(), 'value');
    $tokenizedData = new TokenizedData([$token]);

    expect($tokenizedData->hasMoreTokens())->toBeFalse();
});
