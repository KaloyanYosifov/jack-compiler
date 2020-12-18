<?php

namespace JackCompiler\Exceptions;

class InvalidSyntaxError extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct('Invalid syntax error! ' . $message);
    }
}
