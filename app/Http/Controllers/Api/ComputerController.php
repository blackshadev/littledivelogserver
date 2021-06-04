<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\ViewModels\ApiModels\ComputerListViewModel;
use App\Domain\DataTransferObjects\ComputerData;
use App\Http\Controllers\Controller;
use App\Http\Requests\ComputerCreateRequest;
use App\Models\Computer;
use App\Models\User;
use App\Services\Repositories\ComputerRepository;

class ComputerController extends Controller
{
    private ComputerRepository $repository;

    public function __construct(ComputerRepository $repository)
    {
        $this->authorizeResource(Computer::class, 'computer');
        $this->repository = $repository;
    }

    public function index(User $user)
    {
        return ComputerListViewModel::fromCollection($user->computers);
    }

    public function show(Computer $computer)
    {
        return new ComputerListViewModel($computer);
    }

    public function store(ComputerCreateRequest $request, User $user)
    {
        $computer = $this->repository->createOrFind(ComputerData::fromArray($request->all()), $user);
        return new ComputerListViewModel($computer);
    }
}
