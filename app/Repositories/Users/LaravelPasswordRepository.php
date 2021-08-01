<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Domain\Users\Repositories\PasswordRepository;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\Hash;

final class LaravelPasswordRepository implements PasswordRepository
{
    public function validate(int $userId, string $password): bool
    {
        $user = UserModel::findOrFail($userId);

        return Hash::check($password, $user->password);
    }

    public function updatePassword(int $userId, string $password): void
    {
        $user = UserModel::findOrFail($userId);
        $user->password = $password;
        $user->save();
    }
}
