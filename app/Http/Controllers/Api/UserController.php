<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\TagData;
use App\Http\Controllers\Controller;
use App\Http\Requests\TagCreateRequest;
use App\Http\Requests\TagUpdateRequest;
use App\Models\Tag;
use App\Models\User;
use App\Services\Repositories\TagRepository;
use App\ViewModels\ApiModels\TagViewModel;
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
