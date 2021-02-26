<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Buddy;
use App\Models\Place;
use App\Models\User;

class PlacePolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

//    public function view(User $user, Place $place)
//    {
//        return $user->is($place->user);
//    }
//
//    public function update(User $user, Buddy $buddy)
//    {
//        return $user->is($buddy->user);
//    }

    public function create(User $user)
    {
        return true;
    }
}
