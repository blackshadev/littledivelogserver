<?php

namespace App\Error;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ComputerAlreadyExists extends HttpException
{
    public function __construct(int $serial)
    {
        parent::__construct(409, "Computer with serial {$serial} already exists.");
    }
}
