<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\BuddyData;
use App\Http\Controllers\Controller;
use App\Http\Requests\BuddyCreateRequest;
use App\Http\Requests\BuddyUpdateRequest;
use App\Models\Buddy;
use App\Models\User;
use App\Services\Repositories\BuddyRepository;
use App\ViewModels\ApiModels\BuddyDetailViewModel;
use App\ViewModels\ApiModels\BuddyListViewModel;

class BuddyController extends Controller
{
    private BuddyRepository $repository;

    public function __construct(BuddyRepository $repository)
    {
        $this->authorizeResource(Buddy::class, 'buddy');
        $this->repository = $repository;
    }

    public function index(User $user)
    {
        return BuddyListViewModel::fromCollection($user->buddies);
    }

    public function show(Buddy $buddy)
    {
        return new BuddyDetailViewModel($buddy);
    }

    public function update(Buddy $buddy, BuddyUpdateRequest $request)
    {
        $this->repository->update($buddy, BuddyData::fromArray($request->all()));
        return new BuddyDetailViewModel($buddy);
    }

    public function store(User $user, BuddyCreateRequest $request)
    {
        $buddy = new Buddy();
        $buddy->user()->associate($user);
        $this->repository->update($buddy, BuddyData::fromArray($request->all()));
        return new BuddyDetailViewModel($buddy);
    }
}
