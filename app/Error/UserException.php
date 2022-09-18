<?php

declare(strict_types=1);

namespace App\Error;

interface UserException
{
    public function code(): string;

    public function message(): string;
}
