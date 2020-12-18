<?php

namespace JackCompiler\Reader;

use JackCompiler\Parsers\LineParser;
use JackCompiler\Exceptions\FileNotFound;

class FileReader
{
    protected LineParser $lineParser;

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
            $line = $this->lineParser->handle($line);

            if (!$line) {
                continue;
            }

            yield $line;
        }

        fclose($file);
    }
}
