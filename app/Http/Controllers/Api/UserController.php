<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\ViewModels\ApiModels\UserProfileViewModel;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function profile(User $user)
    {
        $this->authorize('profile', $user);

        return new UserProfileViewModel($user);
    }
}
