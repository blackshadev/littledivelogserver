<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\EquipmentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\EquipmentRequest;
use App\Models\User;
use App\Services\Repositories\EquipmentRepository;
use App\ViewModels\ApiModels\UserEquipmentViewModel;
use App\ViewModels\ApiModels\UserProfileViewModel;

class UserController extends Controller
{
    private EquipmentRepository $equipmentRepository;

    public function __construct(EquipmentRepository $equipmentRepository)
    {
        $this->authorizeResource(User::class, 'user');
        $this->equipmentRepository = $equipmentRepository;
    }

    public function profile(User $user)
    {
        $this->authorize('profile', $user);

        return new UserProfileViewModel($user);
    }

    public function equipment(User $user)
    {
        return new UserEquipmentViewModel($user->equipment);
    }

    public function updateEquipment(User $user, EquipmentRequest $request)
    {
        $equipmentData = EquipmentData::fromArray($user->id, $request->all());
        $equipment = $this->equipmentRepository->findOrCreateForUser($user);
        $this->equipmentRepository->update($equipment, $equipmentData);

        return $this->equipment($user);
    }
}
