<?php

declare(strict_types=1);

namespace App\Error\Auth;

use App\Error\UserException;
use App\Error\UserExceptionTrait;
use Littledev\Tauth\Errors\AuthenticationFailureException;

final class UnverifiedUser extends AuthenticationFailureException implements UserException
{
    use UserExceptionTrait;

    public function __construct()
    {
        $this->errorCode = 'auth.account.not-verified';

        parent::__construct("Email has not been verified. Please verify your email first.");
    }
}
