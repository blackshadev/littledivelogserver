<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dive;
use App\Models\User;
use App\ViewModels\ApiModels\DiveDetailViewModel;
use App\ViewModels\ApiModels\DiveListViewModel;

class DiveController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Dive::class, 'dive');
    }

    public function index(User $user)
    {
        return DiveListViewModel::fromCollection($user->dives);
    }

    public function show(Dive $dive)
    {
        return new DiveDetailViewModel($dive);
    }

    public function samples(Dive $dive)
    {
        return $dive->samples ?? [];
    }
}
