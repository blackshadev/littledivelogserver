<?php

declare(strict_types=1);

namespace App\Error\Auth;

use App\Error\UserException;
use App\Error\UserExceptionTrait;
use Littledev\Tauth\Errors\AuthenticationFailureException;

final class InvalidCredentials extends AuthenticationFailureException implements UserException
{
    use UserExceptionTrait;

    public function __construct(string $message = "Invalid credentials.")
    {
        $this->errorCode = 'auth.credentials-invalid';

        parent::__construct($message);
    }
}
