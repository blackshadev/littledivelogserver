<?php

declare(strict_types=1);

namespace App\Error\Auth;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UserNotFound extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct("User not found");
    }
}
