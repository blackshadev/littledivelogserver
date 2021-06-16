<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\ViewModels\ApiModels\UserEquipmentViewModel;
use App\Application\ViewModels\ApiModels\UserProfileViewModel;
use App\Domain\Equipment\DataTransferObjects\EquipmentData;
use App\Domain\Equipment\Repositories\EquipmentRepository;
use App\Domain\Users\DataTransferObjects\ChangePasswordData;
use App\Domain\Users\DataTransferObjects\UserProfileData;
use App\Domain\Users\Mutators\UpdatePasswordMutator;
use App\Domain\Users\Mutators\UpdateUserProfileMutator;
use App\Domain\Users\Repositories\CurrentUserRepository;
use App\Domain\Users\Repositories\DetailUserRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\EquipmentRequest;
use App\Http\Requests\Equipment\UpdateEquipmentRequest;
use App\Http\Requests\Users\UpdatePasswordRequest;
use App\Http\Requests\Users\UpdateProfileRequest;

class UserProfileController extends Controller
{
    public function __construct(
        private EquipmentRepository $equipmentRepository,
        private CurrentUserRepository $currentUserRepository,
        private DetailUserRepository $detailUserRepository,
        private UpdatePasswordMutator $passwordMutator,
        private UpdateUserProfileMutator $userProfileMutator,
    ) {
    }

    public function show()
    {
        $currentUser = $this->currentUserRepository->getCurrentUser();
        $detailUser = $this->detailUserRepository->findById($currentUser->getId());

        return UserProfileViewModel::fromDetailUser($detailUser);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->getCurrentUser();
        $data = UserProfileData::fromArray($request->all());

        $this->userProfileMutator->setData($user, $data);

        $detailUser = $this->detailUserRepository->findById($user->getId());
        return UserProfileViewModel::fromDetailUser($detailUser);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->getCurrentUser();
        $data = ChangePasswordData::fromArray($request->all());

        $this->passwordMutator->setData($user, $data);

        return response(null, 201);
    }

    public function equipment(EquipmentRequest $request)
    {
        $equipment = $request->getEquipment();

        return new UserEquipmentViewModel($equipment);
    }

    public function updateEquipment(UpdateEquipmentRequest $request)
    {
        $equipmentData = EquipmentData::fromArray($request->all());
        $equipment = $request->getEquipment();

        $this->equipmentRepository->setData($equipment, $equipmentData);
        $this->equipmentRepository->save($equipment);

        return new UserEquipmentViewModel($equipment);
    }
}
