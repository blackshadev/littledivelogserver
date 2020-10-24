<?php

declare(strict_types=1);

namespace Littledev\Tauth\Errors;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidJWTException extends HttpException
{
    public function __construct($message = "Invalid JWT.")
    {
        parent::__construct(401, $message);
    }
}
