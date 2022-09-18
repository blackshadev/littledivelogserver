<?php

declare(strict_types=1);

namespace App\Error;

trait UserExceptionTrait
{
    private readonly string $errorCode;

    public function code(): string
    {
        return $this->errorCode;
    }

    public function message(): string
    {
        return $this->message;
    }
}
