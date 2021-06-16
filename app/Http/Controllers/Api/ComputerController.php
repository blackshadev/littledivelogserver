<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\ViewModels\ApiModels\ComputerListViewModel;
use App\Domain\Computers\DataTransferObjects\ComputerData;
use App\Domain\Computers\Entities\DetailComputer;
use App\Domain\Computers\Repositories\ComputerRepository;
use App\Domain\Computers\Repositories\DetailComputerRepository;
use App\Domain\Support\Arrg;
use App\Http\Controllers\Controller;
use App\Http\Requests\Computers\ComputerCreateRequest;
use App\Http\Requests\Computers\ComputerRequest;
use App\Models\User;

class ComputerController extends Controller
{
    public function __construct(
        private ComputerRepository $repository,
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

    public function store(ComputerCreateRequest $request, User $user)
    {
        $computer = $this->repository->create($user->id, ComputerData::fromArray($request->all()));
        $this->repository->save($computer);

        $detailComputer = $this->detailComputerRepository->findById($computer->getId());
        return ComputerListViewModel::fromDetailModel($detailComputer);
    }
}
