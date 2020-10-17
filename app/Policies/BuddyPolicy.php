<?php

namespace App\Policies;

use App\Models\Buddy;
use App\Models\Dive;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BuddyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Buddy $buddy)
    {
        return $user->is($buddy->user);
    }

    public function update(User $user, Buddy $buddy)
    {
        return $user->is($buddy->user);
    }

    public function create(User $user)
    {
        return true;
    }
}
