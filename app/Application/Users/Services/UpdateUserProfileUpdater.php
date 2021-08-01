<?php

declare(strict_types=1);

namespace App\Application\Users\Services;

use App\Application\Users\DataTransferObjects\UserProfileData;
use App\Domain\Users\Entities\User;
use App\Domain\Users\Repositories\UserRepository;

final class UpdateUserProfileUpdater
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function setData(User $user, UserProfileData $data): void
    {
        $user->setName($data->getName());

        $this->userRepository->save($user);
    }
}
