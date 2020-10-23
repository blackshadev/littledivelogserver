<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Tag $tag)
    {
        return $user->is($tag->user);
    }

    public function update(User $user, Tag $tag)
    {
        return $user->is($tag->user);
    }

    public function create(User $user)
    {
        return true;
    }
}
