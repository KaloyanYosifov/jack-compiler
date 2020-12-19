<?php

namespace Tests;

// ..

use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;

function getTestFilePath(string $filename): string
{
    return TEST_ROOT . DIRECTORY_SEPARATOR . 'test-files' . DIRECTORY_SEPARATOR . $filename;
}

function assertComplexCompiledData(
    ComplexCompiledData $complexCompiledData,
    int $dataIndex,
    CompilationType $compilationType,
    string $expectedValue
) {
    expect($compilationType->equals($complexCompiledData->getDataFrom($dataIndex)->getType()))->toBeTrue();
    expect($expectedValue)->toEqual($complexCompiledData->getDataFrom($dataIndex)->getValue());
}
