<?php

declare(strict_types=1);

namespace App\Application\Dives\Services;

use App\Application\Dives\DataTransferObjects\DiveData;
use App\Domain\Computers\Repositories\ComputerRepository;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\Users\Entities\User;

final class DiveCreator
{
    public function __construct(
        private DiveUpdater $diveUpdater,
        private ComputerRepository $computerRepository,
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

        $computer = $diveData->getComputerId() !== null ?
            $this->computerRepository->findById($diveData->getComputerId()) : null;
        $dive->setComputer($computer);

        if (!is_null($computer) && !is_null($dive->getFingerprint())) {
            $computer->updateLastRead($dive->getDate(), $dive->getFingerprint());
        }

        if ($diveData->getSamples() !== null) {
            $dive->setSamples($diveData->getSamples());
        }

        $diveId = $this->diveUpdater->update($dive, $diveData);

        if (!is_null($computer)) {
            $this->computerRepository->save($computer);
        }

        return $diveId;
    }
}
