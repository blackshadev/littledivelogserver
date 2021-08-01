<?php

declare(strict_types=1);

namespace App\Application\Users\Services;

use App\Application\Users\DataTransferObjects\ChangePasswordData;
use App\Application\Users\Errors\InvalidPassword;
use App\Domain\Users\Entities\User;
use App\Domain\Users\Repositories\PasswordRepository;

final class UpdatePasswordUpdater
{
    public function __construct(
        private PasswordRepository $passwordRepository,
    ) {
    }

    public function setData(User $user, ChangePasswordData $data): void
    {
        if (!$this->passwordRepository->validate($user->getId(), $data->getOldPassword())) {
            throw new InvalidPassword();
        }

        $this->passwordRepository->updatePassword($user->getId(), $data->getNewPassword());
    }
}
