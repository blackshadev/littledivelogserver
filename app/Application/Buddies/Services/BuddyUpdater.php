<?php

declare(strict_types=1);

namespace App\Application\Buddies\Services;

use App\Domain\Buddies\DataTransferObjects\BuddyData;
use App\Domain\Buddies\Entities\Buddy;
use App\Domain\Buddies\Repositories\BuddyRepository;

final class BuddyUpdater
{
    public function __construct(
        private BuddyRepository $buddyRepository,
    ) {
    }

    public function update(Buddy $buddy, BuddyData $buddyData): void
    {
        $buddy->setName($buddyData->getName());
        $buddy->setColor($buddyData->getColor());
        $buddy->setEmail($buddyData->getEmail());

        $this->buddyRepository->save($buddy);
    }
}
