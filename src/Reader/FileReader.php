<?php

namespace JackCompiler\Reader;

use JackCompiler\Parsers\LineParser;
use JackCompiler\Exceptions\FileNotFound;

class FileReader
{
    protected $lineParser;

    public function __construct(LineParser $lineParser)
    {
        $this->lineParser = $lineParser;
    }

    public function readFile(string $filename): \Generator
    {
        if (!file_exists($filename)) {
            throw new FileNotFound($filename);
        }

        /**
         * @var resource $file
         */
        $file = fopen($filename, 'rb');

        while ($line = fgets($file, 4096)) {
            yield $this->lineParser->handle($line);
        }

        fclose($file);
    }
}
