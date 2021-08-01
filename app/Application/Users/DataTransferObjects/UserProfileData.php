<?php

declare(strict_types=1);

namespace App\Application\Users\DataTransferObjects;

final class UserProfileData
{
    private ?string $name;

    public static function fromArray(array $data): self
    {
        $userData = new self();
        $userData->name = $data['name'] ?? null;

        return $userData;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
