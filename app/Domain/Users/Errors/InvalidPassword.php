<?php

declare(strict_types=1);

namespace App\Domain\Users\Errors;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidPassword extends HttpException
{
    public function __construct()
    {
        parent::__construct(400, "Invalid old password");
    }
}
