<?php

declare(strict_types=1);

namespace App\Application\Dives\Services;

use App\Application\Dives\DataTransferObjects\DiveData;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\Users\Entities\User;

final class DiveCreator
{
    public function __construct(
        private DiveUpdater $diveUpdater,
    ) {
    }

    public function create(User $user, DiveData $diveData): DiveId
    {
        $dive = Dive::new(
            userId: $user->getId(),
            date: $diveData->getDate(),
            divetime: $diveData->getDivetime(),
            maxDepth: $diveData->getMaxDepth(),
            fingerprint: $diveData->getFingerprint(),
        );

        if ($diveData->getSamples() !== null) {
            $dive->setSamples($diveData->getSamples());
        }

        return $this->diveUpdater->update($dive, $diveData);
    }
}
