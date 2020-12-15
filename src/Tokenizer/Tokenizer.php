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
        foreach ($this->fileReader->readFile($filename) as $line) {
            dump($line);
        }

        return new TokenizedData([]);
    }
}
