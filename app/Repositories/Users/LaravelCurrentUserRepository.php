<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Domain\Users\Entities\User;
use App\Domain\Users\Repositories\CurrentUserRepository;
use App\Models\User as UserModel;
use Webmozart\Assert\Assert;

final class LaravelCurrentUserRepository implements CurrentUserRepository
{
    public function getCurrentUser(): User
    {
        $user = auth()->user();
        Assert::isInstanceOf($user, UserModel::class);

        return new User($user->id, $user->name, $user->email, $user->origin);
    }

    public function isLoggedIn(): bool
    {
        return auth()->check();
    }
}
