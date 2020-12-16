<?php

namespace JackCompiler\Reader;

use JackCompiler\Exceptions\FileNotFound;

class FileReader
{
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
            $line = trim($line);

            // skip line
            // since it is a comment
            if (preg_match('~^(//|/\*\*)~', $line)) {
                continue;
            }

            $inlineCommentsToRemove = [
                '//',
                '/**',
                '/*',
            ];

            foreach ($inlineCommentsToRemove as $inlineCommentToRemove) {
                // remove comment from line if it doesnt start with it
                if (($foundCommentPos = strpos($line, $inlineCommentToRemove)) !== false) {
                    $line = substr_replace($line, '', $foundCommentPos);
                }
            }

            $line = trim($line);

            // skip empty line
            if (!$line) {
                continue;
            }

            yield $line;
        }

        fclose($file);
    }
}
