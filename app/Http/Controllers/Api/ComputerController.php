<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Computers\DataTransferObjects\ComputerData;
use App\Application\Computers\Services\ComputerUpserter;
use App\Application\Computers\ViewModels\ComputerListViewModel;
use App\Domain\Computers\Entities\DetailComputer;
use App\Domain\Computers\Repositories\DetailComputerRepository;
use App\Domain\Support\Arrg;
use App\Http\Controllers\Controller;
use App\Http\Requests\Computers\ComputerRequest;
use App\Http\Requests\Computers\ComputerUpsertRequest;
use App\Models\User;

class ComputerController extends Controller
{
    public function __construct(
        private ComputerUpserter $creator,
        private DetailComputerRepository $detailComputerRepository,
    ) {
    }

    public function index(User $user)
    {
        return Arrg::map(
            $this->detailComputerRepository->listForUser($user->id),
            fn (DetailComputer $computer) => ComputerListViewModel::fromDetailModel($computer)
        );
    }

    public function show(ComputerRequest $request)
    {
        $detailComputer = $this->detailComputerRepository->findById($request->getComputerId());
        return ComputerListViewModel::fromDetailModel($detailComputer);
    }

    public function upsert(ComputerUpsertRequest $request)
    {
        $computer = $this->creator->create($request->getCurrentUser(), ComputerData::fromArray($request->validated()));

        $detailComputer = $this->detailComputerRepository->findById($computer->getId());
        return ComputerListViewModel::fromDetailModel($detailComputer);
    }
}
