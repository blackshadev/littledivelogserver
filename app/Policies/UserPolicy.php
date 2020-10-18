<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function profile(User $user, User $target)
    {
        return $user->id === $target->id;
    }
}
