<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\DiveData;
use App\Http\Controllers\Controller;
use App\Http\Requests\DiveUpdateRequest;
use App\Models\Dive;
use App\Models\User;
use App\Services\Dives\DiveRepository;
use App\ViewModels\ApiModels\DiveDetailViewModel;
use App\ViewModels\ApiModels\DiveListViewModel;
use Illuminate\Http\Request;

class DiveController extends Controller
{
    private DiveRepository $updater;

    public function __construct(DiveRepository $updater)
    {
        $this->authorizeResource(Dive::class, 'dive');
        $this->updater = $updater;
    }

    public function index(User $user)
    {
        return DiveListViewModel::fromCollection($user->dives);
    }

    public function show(Dive $dive)
    {
        return new DiveDetailViewModel($dive);
    }

    public function samples(Dive $dive)
    {
        return $dive->samples ?? [];
    }

    public function update(Dive $dive, DiveUpdateRequest $request)
    {
        $this->updater->update($dive, DiveData::fromArray($request->all()));
        return new DiveDetailViewModel($dive);
    }
}
