<?php

declare(strict_types=1);

namespace App\Domain\DataTransferObjects;

use App\Models\User;

class NewDiveData extends DiveData
{
    private User $user;

    public static function fromArray(array $data): self
    {
        $diveData = new self();
        $diveData->setData($data);
        return $diveData;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
