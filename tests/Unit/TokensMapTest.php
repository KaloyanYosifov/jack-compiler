<?php

namespace Tests\Unit\JackCompiler\Tokenizer;

use JackCompiler\Tokenizer\TokensMap;
use JackCompiler\Tokenizer\TokenType;

it('returns keyword if it matches', function () {
    $this->assertEquals((new TokensMap())->getTokenType('class')->getValue(), TokenType::KEYWORD()->getValue());
});

it('returns symbol if it matches', function () {
    $this->assertEquals((new TokensMap())->getTokenType(')')->getValue(), TokenType::SYMBOL()->getValue());
});

it('returns integer if it matches', function () {
    $this->assertEquals((new TokensMap())->getTokenType('343')->getValue(), TokenType::INTEGER()->getValue());
});

it('returns string if it matches', function () {
    $this->assertEquals((new TokensMap())->getTokenType('"332"')->getValue(), TokenType::STRING()->getValue());
});

it('returns identifier if it matches', function () {
    $this->assertEquals((new TokensMap())->getTokenType('classDateMate')->getValue(), TokenType::IDENTIFIER()->getValue());
});
