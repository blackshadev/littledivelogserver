<?php

declare(strict_types=1);

namespace Littledev\Tauth\Errors;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class InvalidCredentialsException extends HttpException
{
    public function __construct($message = "Invalid credentials.")
    {
        parent::__construct(401, $message);
    }
}
