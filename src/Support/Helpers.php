<?php

namespace JackCompiler\Support;

class Helpers
{
    public static function cleanOB(string $contents): string
    {
        $lines = explode(PHP_EOL, $contents);
        $cleanedLines = [];

        foreach ($lines as $line) {
            if (!$line = trim($line)) {
                continue;
            }

            $cleanedLines[] = $line;
        }

        return implode(PHP_EOL, $cleanedLines);
    }

    public static function getBaseDirFromFile(string $file): string
    {
        $baseDir = pathinfo($file, PATHINFO_DIRNAME);

        if ($baseDir[0] === DIRECTORY_SEPARATOR) {
            return $baseDir . DIRECTORY_SEPARATOR;
        }

        $baseDir = $baseDir === '.' ? '' : $baseDir;

        if (!$baseDir) {
            return getcwd() . DIRECTORY_SEPARATOR;
        }

        return getcwd() . DIRECTORY_SEPARATOR . $baseDir . DIRECTORY_SEPARATOR;
    }

    public static function getFilesFromDirectory(string $directory, string $extension = '*'): array
    {
        if (!is_dir($directory) || !file_exists($directory)) {
            throw new \InvalidArgumentException('The path you passed is not a directory!');
        }

        $files = scandir($directory);

        if (!$files) {
            return [];
        }

        // if the directory ends with .
        // this will remove it
        $directory = (string) preg_replace('~\.$~', '', $directory);
        $filesMatchingExtension = [];

        // if the directory doesnt have an ending separator
        // add one
        if (!preg_match('~\\' . DIRECTORY_SEPARATOR . '$~', $directory)) {
            $directory .= DIRECTORY_SEPARATOR;
        }

        foreach ($files as $file) {
            if (preg_match('~^\.|\.\.$~', $file) || is_dir($directory . $file)) {
                continue;
            }

            if ($extension !== '*' && !preg_match('~\.' . preg_quote($extension) . '$~', $file)) {
                continue;
            }

            $filesMatchingExtension[] = $directory . $file;
        }

        return $filesMatchingExtension;
    }
}
