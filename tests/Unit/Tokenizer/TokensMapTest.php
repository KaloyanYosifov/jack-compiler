<?php

namespace Tests\Unit\Tokenizer;

use JackCompiler\Tokenizer\TokensMap;
use JackCompiler\Tokenizer\TokenType;
use JackCompiler\Exceptions\InvalidIdentifierName;

$keywords = [
    'class', 'constructor', 'function', 'method',
    'field', 'static', 'var', 'int', 'char', 'boolean',
    'void', 'true', 'false', 'null', 'this', 'let', 'do',
    'if', 'else', 'while', 'return',
];
$symbols = [
    '{', '}', '(', ')', '[', ']', '.', ',', ';', '+', '-', '*',
    '/', '&', ',', '<', '>', '=', '~', '|',
];

it(
    'returns keyword if it matches',
    fn(string $keyword) => expect((new TokensMap())->getTokenType($keyword)->getValue())->toBe(TokenType::KEYWORD()->getValue())
)
    ->with($keywords);

it(
    'returns symbol if it matches',
    fn(string $symbol) => expect((new TokensMap())->getTokenType($symbol)->getValue())->toBe(TokenType::SYMBOL()->getValue())
)
    ->with($symbols);

it('returns integer if it matches', function () {
    $this->assertEquals((new TokensMap())->getTokenType('343')->getValue(), TokenType::INTEGER()->getValue());
});

it('returns string if it matches', function () {
    $this->assertEquals((new TokensMap())->getTokenType('"332"')->getValue(), TokenType::STRING()->getValue());
});

it('returns identifier if it matches', function () {
    $this->assertEquals((new TokensMap())->getTokenType('classDateMate')->getValue(), TokenType::IDENTIFIER()->getValue());
});

it('throws an error if identifier is invalid', fn(string $variableName) => (new TokensMap())->getTokenType($variableName))
    ->with([
        '$classMate',
        '1classMate',
        'class$%Mate',
        'classMate"',
        '"classMate',
    ])
    ->expectException(InvalidIdentifierName::class);
