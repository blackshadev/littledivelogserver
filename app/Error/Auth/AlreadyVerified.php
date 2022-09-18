<?php

declare(strict_types=1);

namespace App\Error\Auth;

use InvalidArgumentException;

final class AlreadyVerified extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct("User is already verified");
    }
}
