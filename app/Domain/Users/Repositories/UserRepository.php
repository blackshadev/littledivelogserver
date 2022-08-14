<?php

declare(strict_types=1);

namespace App\Domain\Users\Repositories;

use App\Domain\Users\Entities\User;

interface UserRepository
{
    public function save(User $user): void;

    public function findByEmail(string $email): User|null;

    public function findById(int $id): User|null;
}
