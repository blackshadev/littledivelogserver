<?php

declare(strict_types=1);

namespace App\Domain\Users\Repositories;

interface PasswordRepository
{
    public function validate(int $userId, string $password): bool;

    public function updatePassword(int $userId, string $password): void;
}
