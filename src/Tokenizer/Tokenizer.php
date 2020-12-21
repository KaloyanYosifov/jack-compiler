<?php

namespace JackCompiler\Tokenizer;

use JackCompiler\Reader\FileReader;
use JackCompiler\Parsers\LineParser;

class Tokenizer
{
    protected FileReader $fileReader;
    protected TokensMap $tokensMap;
    protected LineParser $lineParser;

    /**
     * Tokenizer constructor.
     * @param FileReader $fileReader
     * @param TokensMap $tokensMap
     */
    public function __construct(FileReader $fileReader, TokensMap $tokensMap, LineParser $lineParser)
    {
        $this->fileReader = $fileReader;
        $this->tokensMap = $tokensMap;
        $this->lineParser = $lineParser;
    }

    public function handle(string $filename): TokenizedData
    {
        return $this->searchForTokens($this->fileReader->readFile($filename), $filename);
    }

    public function handleStringData(string $data): TokenizedData
    {
        if (!$data) {
            return new TokenizedData([]);
        }

        return $this->searchForTokens(explode(PHP_EOL, $data));
    }

    public static function create(): self
    {
        return new self(new FileReader(new LineParser()), new TokensMap(), new LineParser());
    }

    protected function searchForTokens($lines, string $fileParsing = 'unknown'): TokenizedData
    {
        $tokens = [];
        $lineNumber = 0;
        foreach ($lines as $line) {
            $lineNumber++;

            if (!$tokensFound = $this->parseLineForTokens($line)) {
                continue;
            }

            foreach ($tokensFound as $tokenFound) {
                $tokens[] = new Token(
                    $this->tokensMap->getTokenType($tokenFound),
                    $tokenFound,
                    $lineNumber,
                    $fileParsing
                );
            }
        }

        return new TokenizedData($tokens);
    }

    /**
     * @param string $line
     * @return string[]
     * @throws \JackCompiler\Exceptions\InvalidIdentifierName
     */
    private function parseLineForTokens(string $line): array
    {
        $token = '';
        $tokensFound = [];

        for ($lineIndex = 0, $charactersCount = strlen($line); $lineIndex < $charactersCount; $lineIndex++) {
            $char = $line[$lineIndex];

            if ($char === ' ') {
                if (!$token) {
                    continue;
                }

                $tokensFound[] = $token;
                $token = '';
                continue;
            }

            if ($this->tokensMap->isSymbol($char)) {
                if ($token) {
                    $tokensFound[] = $token;
                }

                $tokensFound[] = $char;
                $token = '';
                continue;
            }

            $token .= $char;
        }

        // if we have a token left
        // add it to tokens found
        if ($token) {
            $tokensFound[] = $token;
        }

        return $tokensFound;
    }
}
