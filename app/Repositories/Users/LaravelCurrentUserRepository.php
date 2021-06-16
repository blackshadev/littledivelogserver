<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Domain\Users\Entities\User;
use App\Domain\Users\Repositories\CurrentUserRepository;
use LogicException;

class LaravelCurrentUserRepository implements CurrentUserRepository
{
    public function getCurrentUser(): User
    {
        $user = auth()->user();

        if ($user === null) {
            throw new LogicException('Not authenticated at this point');
        }

        return new User($user->id, $user->name);
    }

    public function isLoggedIn(): bool
    {
        return auth()->check();
    }
}
