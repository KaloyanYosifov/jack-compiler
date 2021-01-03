<?php

namespace JackCompiler\VMCompiler;

use JackCompiler\CompileEngine\CompiledData;
use JackCompiler\CompileEngine\ComplexCompiledData;

class VMCompiler
{
    public function handle(ComplexCompiledData $complexCompiledData): void
    {
        /**
         * @var ComplexCompiledData|CompiledData $compiledData
         */
        foreach ($complexCompiledData as $compiledData) {
            dump($compiledData->getType()->getValue());
        }
    }
}
