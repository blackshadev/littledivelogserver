<?php

declare(strict_types=1);

namespace App\Application\Dives\Services;

use App\Application\Dives\DataTransferObjects\DiveData;
use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\Users\Entities\User;

interface DiveCreatorInterface
{
    public function create(User $user, DiveData $diveData): DiveId;
}
