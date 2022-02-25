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
use App\Http\Requests\Dives\DiveCreateRequest;
use App\Http\Requests\Dives\DiveDeleteRequest;
use App\Http\Requests\Dives\DiveMergeRequest;
use App\Http\Requests\Dives\DiveSamplesRequest;
use App\Http\Requests\Dives\DiveSearchRequest;
use App\Http\Requests\Dives\DiveUpdateRequest;
use App\Http\Requests\Dives\ListDiveRequest;
use App\Http\Requests\Dives\ShowDiveRequest;

final class DiveController extends Controller
{
    public function index(ListDiveRequest $request, DiveSummaryRepository $diveSummaryRepository)
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

    public function show(ShowDiveRequest $request)
    {
        return DiveDetailViewModel::fromDive($request->getDive());
    }

    public function samples(DiveSamplesRequest $request)
    {
        return $request->getDiveSamples()->samples();
    }

    public function update(DiveUpdateRequest $request, DiveUpdater $diveUpdater, DiveRepository $diveRepository)
    {
        $dive = $request->getDive();
        $diveData = DiveData::fromArray($request->all());

        $id = $diveUpdater->update($dive, $diveData);

        return DiveDetailViewModel::fromDive($diveRepository->findById($id));
    }

    public function store(DiveCreateRequest $request, DiveCreator $diveCreator, DiveRepository $diveRepository)
    {
        $diveData = DiveData::fromArray($request->all());

        $id = $diveCreator->create($request->getCurrentUser(), $diveData);

        return DiveDetailViewModel::fromDive($diveRepository->findById($id));
    }

    public function merge(
        DiveMergeRequest $request,
        DiveBatchRepository $diveBatchRepository,
        DiveMerger $diveMerger,
    ) {
        $dives = $diveBatchRepository->findByIds($request->validated()['dives']);

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
