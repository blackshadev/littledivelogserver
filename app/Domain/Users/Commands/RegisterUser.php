<?php

declare(strict_types=1);

namespace App\Domain\Users\Commands;

final class RegisterUser
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $origin,
    ) {
    }
}
