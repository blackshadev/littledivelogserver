<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\ComputerData;
use App\Http\Controllers\Controller;
use App\Http\Requests\ComputerCreateRequest;
use App\Models\Computer;
use App\Models\User;
use App\Services\Repositories\ComputerRepository;
use App\ViewModels\ApiModels\ComputerListViewModel;

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
        $this->repository->create(ComputerData::fromArray($request->all()), $user);

    }
}
