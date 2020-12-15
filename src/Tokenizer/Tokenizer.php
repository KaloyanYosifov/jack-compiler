<?php

namespace JackCompiler\Tokenizer;

use JackCompiler\Reader\FileReader;

class Tokenizer
{
    /**
     * @var FileReader
     */
    protected FileReader $fileReader;
    /**
     * @var TokensMap
     */
    protected TokensMap $tokensMap;

    /**
     * Tokenizer constructor.
     * @param FileReader $fileReader
     * @param TokensMap $tokensMap
     */
    public function __construct(FileReader $fileReader, TokensMap $tokensMap)
    {
        $this->fileReader = $fileReader;
        $this->tokensMap = $tokensMap;
    }

    public function handle(string $filename): TokenizedData
    {
        $tokens = [];
        foreach ($this->fileReader->readFile($filename) as $line) {
            $token = '';

            for ($lineIndex = 0, $charactersCount = strlen($line); $lineIndex < $charactersCount; $lineIndex++) {
                $char = $line[$lineIndex];

                if ($char === ' ') {
                    if (!$token) {
                        continue;
                    }

                    $tokens[] = new Token($this->tokensMap->getTokenType($token), $token);
                    $token = '';
                    continue;
                }

                if ($this->tokensMap->isSymbol($char)) {
                    if ($token) {
                        $tokens[] = new Token($this->tokensMap->getTokenType($token), $token);
                    }

                    $tokens[] = new Token($this->tokensMap->getTokenType($char), $char);
                    $token = '';
                    continue;
                }

                $token .= $char;
            }
        }

        return new TokenizedData($tokens);
    }
}
