<?php

namespace Tests\Unit\CompileEngine\Compilations;

use JackCompiler\Tokenizer\Tokenizer;
use JackCompiler\CompileEngine\CompilationType;
use JackCompiler\CompileEngine\Compilations\Statements\StatementsCompilation;

use function Tests\assertComplexCompiledData;

it('compiles statements', function () {
    $implementation = <<<EOF
        if (test) {
        }
        
        while (doing) {
        
        }
        
        let test = "test";
        
        do test();
        
        return help;
    EOF;

    $tokenizedData = Tokenizer::create()->handleStringData($implementation);
    $compiledData = StatementsCompilation::create()->compile($tokenizedData);

    $this->assertTrue(CompilationType::STATEMENTS()->equals($compiledData->getType()));
    assertComplexCompiledData($compiledData, 0, CompilationType::IF_STATEMENT());
    assertComplexCompiledData($compiledData, 1, CompilationType::WHILE_STATEMENT());
    assertComplexCompiledData($compiledData, 2, CompilationType::LET_STATEMENT());
    assertComplexCompiledData($compiledData, 3, CompilationType::DO_STATEMENT());
    assertComplexCompiledData($compiledData, 4, CompilationType::RETURN_STATEMENT());

    $this->assertNull($compiledData->getDataFrom(5));
});
