<?php

declare(strict_types=1);

namespace App\Domain\Users\Repositories;

use App\Domain\Users\Entities\User;

interface CurrentUserRepository
{
    public function getCurrentUser(): User;

    public function isLoggedIn(): bool;
}
