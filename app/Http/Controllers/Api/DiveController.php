<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Dives\CommandObjects\FindDivesCommand;
use App\Application\Dives\DataTransferObjects\DiveData;
use App\Application\Dives\Services\DiveCreator;
use App\Application\Dives\Services\DiveFinder;
use App\Application\Dives\Services\DiveUpdater;
use App\Application\Dives\ViewModels\DiveDetailViewModel;
use App\Application\Dives\ViewModels\DiveListViewModel;
use App\Domain\Dives\Entities\DiveSummary;
use App\Domain\Dives\Repositories\DiveSummaryRepository;
use App\Domain\Support\Arrg;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthenticatedRequest;
use App\Http\Requests\DiveMergeRequest;
use App\Http\Requests\Dives\DiveCreateRequest;
use App\Http\Requests\Dives\DiveRequest;
use App\Http\Requests\Dives\DiveSearchRequest;
use App\Http\Requests\Dives\DiveUpdateRequest;
use App\Services\DiveMerger\DiveMergerService;

class DiveController extends Controller
{
    public function __construct(
        private DiveSummaryRepository $diveSummaryRepository,
        private DiveUpdater $diveUpdater,
        private DiveCreator $diveCreator,
        private DiveFinder $diveFinder,
    ) {
    }

    public function index(AuthenticatedRequest $request)
    {
        $user = $request->getCurrentUser();
        $dives = $this->diveSummaryRepository->listForUser($user);

        return Arrg::map(
            $dives,
            fn (DiveSummary $dive) => DiveListViewModel::fromDiveSummary($dive)
        );
    }

    public function search(DiveSearchRequest $request)
    {
        $search = FindDivesCommand::forUser(
            $request->getCurrentUser()->getId(),
            $request->query()
        );

        $dives = $this->diveFinder->search($search);

        return Arrg::map(
            $dives,
            fn (DiveSummary $dive) => DiveListViewModel::fromDiveSummary($dive)
        );
    }

    public function show(DiveRequest $request)
    {
        return DiveDetailViewModel::fromDive($request->getDive());
    }

    public function samples(DiveRequest $request)
    {
        return $request->getDive()->getSamples();
    }

    public function update(DiveUpdateRequest $request)
    {
        $dive = $request->getDive();
        $diveData = DiveData::fromArray($request->all());

        $this->diveUpdater->update($dive, $diveData);

        return DiveDetailViewModel::fromDive($dive);
    }

    public function store(DiveCreateRequest $request)
    {
        $diveData = DiveData::fromArray($request->all());

        $dive = $this->diveCreator->create($request->getCurrentUser(), $diveData);

        return DiveDetailViewModel::fromDive($dive);
    }

    public function merge(
        DiveMergeRequest $request,
//        User $user,
        DiveMergerService $diveMergerService
    ) {
        abort(400, 'Not yet implemented');
//        $dives = Dive::find([$request->get('dives')]);
//        if (count($dives) !== 0 && $dives[0]->user_id === $user->id) {
//            abort(403);
//        }
//        $newDive = $diveMergerService->mergeDives($dives);
//        $this->repository->update(new Dive(), $newDive);
//        $this->repository->removeMany($dives);
    }
}
