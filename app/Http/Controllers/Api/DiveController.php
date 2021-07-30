<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Dives\CommandObjects\FindDivesCommand;
use App\Application\Dives\DataTransferObjects\DiveData;
use App\Application\Dives\Services\DiveCreator;
use App\Application\Dives\Services\DiveFinder;
use App\Application\Dives\Services\DiveUpdater;
use App\Application\Dives\Services\Mergers\DiveMerger;
use App\Application\Dives\ViewModels\DiveDetailViewModel;
use App\Application\Dives\ViewModels\DiveListViewModel;
use App\Domain\Dives\Entities\DiveSummary;
use App\Domain\Dives\Repositories\DiveBatchRepository;
use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Dives\Repositories\DiveSummaryRepository;
use App\Domain\Support\Arrg;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthenticatedRequest;
use App\Http\Requests\Dives\DiveCreateRequest;
use App\Http\Requests\Dives\DiveDeleteRequest;
use App\Http\Requests\Dives\DiveMergeRequest;
use App\Http\Requests\Dives\DiveRequest;
use App\Http\Requests\Dives\DiveSearchRequest;
use App\Http\Requests\Dives\DiveUpdateRequest;

class DiveController extends Controller
{
    public function index(AuthenticatedRequest $request, DiveSummaryRepository $diveSummaryRepository)
    {
        $user = $request->getCurrentUser();
        $dives = $diveSummaryRepository->listForUser($user);

        return Arrg::map(
            $dives,
            fn (DiveSummary $dive) => DiveListViewModel::fromDiveSummary($dive)
        );
    }

    public function search(DiveSearchRequest $request, DiveFinder $diveFinder)
    {
        $search = FindDivesCommand::forUser(
            $request->getCurrentUser()->getId(),
            $request->query()
        );

        $dives = $diveFinder->search($search);

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

    public function update(DiveUpdateRequest $request, DiveUpdater $diveUpdater)
    {
        $dive = $request->getDive();
        $diveData = DiveData::fromArray($request->all());

        $diveUpdater->update($dive, $diveData);

        return DiveDetailViewModel::fromDive($dive);
    }

    public function store(DiveCreateRequest $request, DiveCreator $diveCreator)
    {
        $diveData = DiveData::fromArray($request->all());

        $dive = $diveCreator->create($request->getCurrentUser(), $diveData);

        return DiveDetailViewModel::fromDive($dive);
    }

    public function merge(
        DiveMergeRequest $request,
        DiveBatchRepository $diveBatchRepository,
        DiveMerger $diveMerger,
    ) {
        $dives = $diveBatchRepository->findByIds($request->vali('dives'));

        $dive = $diveMerger->merge($dives);

        $diveBatchRepository->replace($dives, $dive);

        return DiveDetailViewModel::fromDive($dive);
    }

    public function delete(DiveDeleteRequest $request, DiveRepository $diveRepository)
    {
        $diveRepository->remove($request->getDive());

        return response()->noContent();
    }
}
