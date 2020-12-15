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
     * Tokenizer constructor.
     * @param FileReader $fileReader
     */
    public function __construct(FileReader $fileReader)
    {
        $this->fileReader = $fileReader;
    }

    public function handle(string $filename): TokenizedData
    {
        $tokens = [];
        foreach ($this->fileReader->readFile($filename) as $line) {
            $token = '';

            for ($lineIndex = 0, $charactersCount = strlen($line); $lineIndex < $charactersCount; $lineIndex++) {
                $char = $line[$lineIndex];

                if ($token && $char === ' ') {
                    $tokens[] = new Token($this->getTokenType($token), $token);
                }
            }
        }

        return new TokenizedData($tokens);
    }

    protected function getTokenType(string $token): TokenType
    {
    }
}
