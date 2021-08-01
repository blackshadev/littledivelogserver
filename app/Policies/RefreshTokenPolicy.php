<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\RefreshToken;
use App\Models\User;

final class RefreshTokenPolicy
{
    public function delete(User $target, RefreshToken $refreshToken)
    {
        return $refreshToken->user->id === $target->id;
    }
}
