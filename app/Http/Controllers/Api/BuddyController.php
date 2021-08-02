<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Buddies\DataTransferObjects\BuddyData;
use App\Application\Buddies\Services\BuddyCreator;
use App\Application\Buddies\Services\BuddyUpdater;
use App\Application\Buddies\ViewModels\BuddyDetailViewModel;
use App\Application\Buddies\ViewModels\BuddyListViewModel;
use App\Domain\Buddies\Entities\DetailBuddy;
use App\Domain\Buddies\Repositories\BuddyRepository;
use App\Domain\Buddies\Repositories\DetailBuddyRepository;
use App\Domain\Support\Arrg;
use App\Http\Controllers\Controller;
use App\Http\Requests\Buddies\CreateBuddyRequest;
use App\Http\Requests\Buddies\DeleteBuddyRequest;
use App\Http\Requests\Buddies\ShowBuddyRequest;
use App\Http\Requests\Buddies\UpdateBuddyRequest;
use App\Models\User;

final class BuddyController extends Controller
{
    public function __construct(
        private BuddyCreator $creator,
        private BuddyUpdater $updater,
        private DetailBuddyRepository $detailRepository,
    ) {
    }

    public function index(User $user)
    {
        return Arrg::map(
            $this->detailRepository->listForUser($user->id),
            fn (DetailBuddy $buddy) => BuddyListViewModel::fromDetailBuddy($buddy)
        );
    }

    public function show(ShowBuddyRequest $request)
    {
        $detailBuddy = $this->detailRepository->findById($request->getBuddyId());
        return BuddyDetailViewModel::fromDetailBuddy($detailBuddy);
    }

    public function update(UpdateBuddyRequest $request)
    {
        $buddyData = BuddyData::fromArray($request->all());
        $buddy = $request->getBuddy();

        $this->updater->update($buddy, $buddyData);

        $detailTag = $this->detailRepository->findById($buddy->getId());
        return BuddyDetailViewModel::fromDetailBuddy($detailTag);
    }

    public function store(CreateBuddyRequest $request)
    {
        $data = BuddyData::fromArray($request->all());
        $user = $request->getCurrentUser();

        $buddy = $this->creator->create($user, $data);

        $detailTag = $this->detailRepository->findById($buddy->getId());
        return BuddyDetailViewModel::fromDetailBuddy($detailTag);
    }

    public function delete(DeleteBuddyRequest $request, BuddyRepository $repository)
    {
        $repository->remove($request->getBuddy());
        return response()->noContent();
    }
}
