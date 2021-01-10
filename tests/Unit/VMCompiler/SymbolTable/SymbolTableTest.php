<?php

namespace Unit\Tests\VMCompiler;

use JackCompiler\VMCompiler\SymbolTable\SymbolTable;
use JackCompiler\VMCompiler\SymbolTable\SymbolKindType;

it('has a parent node', function () {
    $parent = new SymbolTable();
    $child = new SymbolTable($parent);

    expect($child->getParent())->toEqual($parent);
});

it('can look for symbol', function () {
   $table = new SymbolTable();
   $table->addSymbol('test2', 'int', SymbolKindType::FIELD());
   expect($table->findByName('test2')->name)->toEqual('test2');
});
