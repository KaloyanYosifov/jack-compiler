<?php

namespace Tests;

// ..

function getTestFilePath(string $filename): string
{
    return TEST_ROOT . DIRECTORY_SEPARATOR . 'test-files' . DIRECTORY_SEPARATOR . $filename;
}
