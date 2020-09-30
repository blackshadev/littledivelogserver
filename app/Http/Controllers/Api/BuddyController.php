<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buddy;
use App\Models\User;
use App\ViewModels\ApiModels\BuddyDetailViewModel;
use App\ViewModels\ApiModels\BuddyListViewModel;

class BuddyController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Buddy::class, 'buddy');
    }

    public function index(User $user)
    {
        return BuddyListViewModel::fromCollection($user->buddies);
    }

    public function show(User $user)
    {
        return new BuddyDetailViewModel($user->buddies);
    }
}
