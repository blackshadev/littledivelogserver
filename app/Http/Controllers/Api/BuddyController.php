<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\ViewModels\ApiModels\BuddyDetailViewModel;
use App\Application\ViewModels\ApiModels\BuddyListViewModel;
use App\Domain\Buddies\DataTransferObjects\BuddyData;
use App\Domain\Buddies\Entities\DetailBuddy;
use App\Domain\Buddies\Repositories\BuddyRepository;
use App\Domain\Buddies\Repositories\DetailBuddyRepository;
use App\Domain\Support\Arrg;
use App\Http\Controllers\Controller;
use App\Http\Requests\Buddies\BuddyCreateRequest;
use App\Http\Requests\Buddies\BuddyRequest;
use App\Http\Requests\Buddies\BuddyUpdateRequest;
use App\Models\User;

class BuddyController extends Controller
{
    public function __construct(
        private BuddyRepository $repository,
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

    public function show(BuddyRequest $request)
    {
        $detailBuddy = $this->detailRepository->findById($request->getBuddyId());
        return BuddyDetailViewModel::fromDetailBuddy($detailBuddy);
    }

    public function update(BuddyUpdateRequest $request)
    {
        $buddyData = BuddyData::fromArray($request->all());
        $buddy = $request->getBuddy();

        $this->repository->setData($buddy, $buddyData);
        $this->repository->save($buddy);

        $detailTag = $this->detailRepository->findById($buddy->getId());
        return BuddyDetailViewModel::fromDetailBuddy($detailTag);
    }

    public function store(User $user, BuddyCreateRequest $request)
    {
        $buddy = $this->repository->create($user->id, BuddyData::fromArray($request->all()));
        $this->repository->save($buddy);

        $detailTag = $this->detailRepository->findById($buddy->getId());
        return BuddyDetailViewModel::fromDetailBuddy($detailTag);
    }
}
