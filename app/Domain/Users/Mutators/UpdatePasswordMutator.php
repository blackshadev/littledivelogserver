<?php

declare(strict_types=1);

namespace App\Domain\Users\Mutators;

use App\Domain\Users\DataTransferObjects\ChangePasswordData;
use App\Domain\Users\Entities\User;
use App\Domain\Users\Errors\InvalidPassword;
use App\Domain\Users\Repositories\PasswordRepository;

class UpdatePasswordMutator
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
