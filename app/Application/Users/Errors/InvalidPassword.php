<?php

declare(strict_types=1);

namespace App\Application\Users\Errors;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class InvalidPassword extends HttpException
{
    public function __construct()
    {
        parent::__construct(400, "Invalid old password");
    }
}
