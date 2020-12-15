<?php

namespace JackCompiler\Exceptions;

class InvalidIdentifierName extends \Exception
{
    public function __construct(string $identifier)
    {
        parent::__construct("The $identifier is not a valid identifier!");
    }
}
