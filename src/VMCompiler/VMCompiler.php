<?php

namespace JackCompiler\VMCompiler;

use JackCompiler\CompileEngine\CompiledData;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\ComplexCompiledData;
use JackCompiler\VMCompiler\SymbolTable\SymbolTable;

class VMCompiler
{
    protected SymbolTable $classSymbolTable;
    protected SymbolTable $methodSymbolTable;

    public function __construct()
    {
        $this->classSymbolTable = new SymbolTable();
        $this->methodSymbolTable = new SymbolTable($this->classSymbolTable);
    }

    public function handle(ComplexCompiledData $complexCompiledData): void
    {
        /**
         * @var ComplexCompiledData|CompiledData $compiledData
         */
        foreach ($complexCompiledData as $compiledData) {
            if ($compiledData->getType()->equals(CompilationType::VAR_DEC())) {
            }

            if ($compiledData instanceof ComplexCompiledData) {
                $this->handle($compiledData);
            }
        }
    }
}
