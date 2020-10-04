<?php

namespace App\Policies;

use App\Models\Computer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComputerPolicy
{
    use HandlesAuthorization;

    public function viewAny()
    {
        return true;
    }

    public function view(User $user, Computer $computer)
    {
        return $user->is($computer->user);
    }

    public function create(User $user)
    {
        return true;
    }
}
