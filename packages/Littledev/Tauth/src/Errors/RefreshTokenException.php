<?php

declare(strict_types=1);

namespace Littledev\Tauth\Errors;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RefreshTokenException extends HttpException
{
    public function __construct(string $message = null)
    {
        parent::__construct(401, $message);
    }

    public static function invalidRefreshToken(): self
    {
        return new self('Invalid refresh token');
    }
}
