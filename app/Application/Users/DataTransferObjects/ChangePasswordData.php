<?php

declare(strict_types=1);

namespace App\Application\Users\DataTransferObjects;

class ChangePasswordData
{
    public function __construct(
        private string $newPassword,
        private string $oldPassword,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['new'] ?? '',
            $data['old'] ?? '',
        );
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }
}
