<?php

declare(strict_types=1);

namespace Littledev\Tauth\Errors;

final class InvalidCredentialsException extends AuthenticationFailureException
{
    public function __construct($message = "Invalid credentials.")
    {
        parent::__construct($message);
    }
}
