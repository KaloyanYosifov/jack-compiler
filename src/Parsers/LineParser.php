<?php

namespace JackCompiler\Parsers;

class LineParser
{
    public function handle(string $line): string
    {
        $line = trim($line);
        // skip line
        // since it is a comment
        if (preg_match('~^(//|/\*\*)~', $line)) {
            return '';
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
            return '';
        }

        return $line;
    }
}
