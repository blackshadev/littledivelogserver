<?php

declare(strict_types=1);

namespace App\Error;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class ComputerAlreadyExists extends HttpException
{
    public function __construct(int $serial)
    {
        parent::__construct(409, "Computer with serial {$serial} already exists.");
    }
}
