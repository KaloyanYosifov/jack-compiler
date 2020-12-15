<?php

namespace JackCompiler\Exceptions;

class FileNotFound extends \Exception
{
    public function __construct(string $filename)
    {
        parent::__construct("File is not found! -- $filename");
    }
}
