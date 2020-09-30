<?php

namespace Littledev\Tauth\Errors;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidCredentialsException extends HttpException
{
    public function __construct($message = "Invalid credentials.")
    {
        parent::__construct(401, $message);
    }
}
