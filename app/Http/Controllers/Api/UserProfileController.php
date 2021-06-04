<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\ViewModels\ApiModels\UserEquipmentViewModel;
use App\Application\ViewModels\ApiModels\UserProfileViewModel;
use App\Domain\DataTransferObjects\EquipmentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\EquipmentRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Services\Repositories\EquipmentRepository;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    private EquipmentRepository $equipmentRepository;

    public function __construct(EquipmentRepository $equipmentRepository)
    {
        $this->equipmentRepository = $equipmentRepository;
    }

    public function show(User $user)
    {
        return new UserProfileViewModel($user);
    }

    public function update(User $user, UpdateProfileRequest $request)
    {
        $user->name = $request->input('name');
        $user->save();

        return $this->show($user);
    }

    public function updatePassword(User $user, UpdatePasswordRequest $request)
    {
        if (!Hash::check($request->input('old'), $user->password)) {
            abort(403, 'Invalid old password');
        }

        $user->password = $request->input('new');
        $user->save();

        return response(null, 201);
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

        return new UserEquipmentViewModel($equipment);
    }
}
