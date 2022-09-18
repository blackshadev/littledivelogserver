<?php

declare(strict_types=1);

namespace Littledev\Tauth\Errors;

use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class AuthenticationFailureException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct(401, $message);
    }
}
