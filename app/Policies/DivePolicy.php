<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Dive;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class DivePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Dive $dive)
    {
        return $user->is($dive->user);
    }

    public function update(User $user, Dive $dive)
    {
        return $user->is($dive->user);
    }

    public function delete(User $user, Dive $dive)
    {
        return $user->is($dive->user);
    }

    public function create(User $user)
    {
        return true;
    }
}
