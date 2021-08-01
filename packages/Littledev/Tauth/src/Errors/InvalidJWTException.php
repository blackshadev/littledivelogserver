<?php

declare(strict_types=1);

namespace Littledev\Tauth\Errors;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class InvalidJWTException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct(401, $message);
    }

    public static function malformed(): self
    {
        return new self('malformed JWT given');
    }

    public static function invalid(): self
    {
        return new self('Invalid JWT given');
    }
}
