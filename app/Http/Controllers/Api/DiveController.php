<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\DiveData;
use App\DataTransferObjects\NewDiveData;
use App\Http\Controllers\Controller;
use App\Http\Requests\DiveCreateRequest;
use App\Http\Requests\DiveMergeRequest;
use App\Http\Requests\DiveUpdateRequest;
use App\Models\Dive;
use App\Models\User;
use App\Services\DiveMerger\DiveMergerService;
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
        $diveData = DiveData::fromArray($request->all());
        DiveData::fromArray($request->all());

        $this->repository->update($dive, $diveData);

        return new DiveDetailViewModel($dive);
    }

    public function store(DiveCreateRequest $request, User $user)
    {
        $diveData = NewDiveData::fromArray($request->all());
        $diveData->setUser($user);

        $dive = new Dive();
        $this->repository->update($dive, $diveData);

        return new DiveDetailViewModel($dive);
    }

    public function merge(
        DiveMergeRequest $request,
        User $user,
        DiveMergerService $diveMergerService
    ) {
        $dives = Dive::find([$request->get('dives')]);
        if (count($dives) !== 0 && $dives[0]->user_id === $user->id) {
            abort(403);
        }
        $newDive = $diveMergerService->mergeDives($dives);
        $this->repository->update(new Dive(), $newDive);
        $this->repository->removeMany($dives);
    }
}
