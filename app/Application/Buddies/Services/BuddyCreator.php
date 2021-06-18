<?php

declare(strict_types=1);

namespace App\Application\Buddies\Services;

use App\Domain\Buddies\DataTransferObjects\BuddyData;
use App\Domain\Buddies\Entities\Buddy;
use App\Domain\Buddies\Repositories\BuddyRepository;
use App\Domain\Users\Entities\User;

final class BuddyCreator
{
    public function __construct(
        private BuddyRepository $buddyRepository,
    ) {
    }

    public function create(User $user, BuddyData $buddyData): Buddy
    {
        $buddy = Buddy::new($user->getId(), $buddyData->getName(), $buddyData->getColor(), $buddyData->getEmail());
        $this->buddyRepository->save($buddy);

        return $buddy;
    }
}
