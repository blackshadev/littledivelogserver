<?php

declare(strict_types=1);

namespace App\Application\Buddies\Services;

use App\Application\Buddies\DataTransferObjects\BuddyData;
use App\Domain\Buddies\Entities\Buddy;
use App\Domain\Buddies\Repositories\BuddyRepository;
use App\Domain\Users\Entities\User;

final class BuddyProvider
{
    public function __construct(
        private BuddyRepository $repository
    ) {
    }

    public function make(User $user, BuddyData $buddyData): Buddy
    {
        return Buddy::new($user->getId(), $buddyData->getName(), $buddyData->getColor(), $buddyData->getEmail());
    }

    public function findOrMake(User $user, BuddyData $buddyData): Buddy
    {
        if ($buddyData->getId() === null) {
            return $this->make($user, $buddyData);
        }

        return $this->repository->findById($buddyData->getId());
    }
}
