<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\DiveData;
use App\Http\Controllers\Controller;
use App\Http\Requests\DiveCreateRequest;
use App\Http\Requests\DiveUpdateRequest;
use App\Models\Dive;
use App\Models\User;
use App\Services\Repositories\DiveRepository;
use App\ViewModels\ApiModels\DiveDetailViewModel;
use App\ViewModels\ApiModels\DiveListViewModel;

class DiveController extends Controller
{
    private DiveRepository $repository;

    public function __construct(DiveRepository $updater)
    {
        $this->authorizeResource(Dive::class, 'dive');
        $this->repository = $updater;
    }

    public function index(User $user)
    {
        return DiveListViewModel::fromCollection(
            $user->dives()->orderBy('id', 'desc')->get()
        );
    }

    public function show(Dive $dive)
    {
        return new DiveDetailViewModel($dive);
    }

    public function samples(Dive $dive)
    {
        $this->authorize('view', $dive);

        return $dive->samples ?? [];
    }

    public function update(Dive $dive, DiveUpdateRequest $request)
    {
        $this->repository->update($dive, DiveData::fromArray($request->all()));

        return new DiveDetailViewModel($dive);
    }

    public function store(DiveCreateRequest $request, User $user)
    {
        $dive = new Dive();
        $dive->user()->associate($user);
        $this->repository->update($dive, DiveData::fromArray($request->all()));

        return new DiveDetailViewModel($dive);
    }
}
