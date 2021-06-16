<?php

declare(strict_types=1);

namespace App\Domain\Buddies\Repositories;

use App\Domain\Buddies\DataTransferObjects\BuddyData;
use App\Domain\Buddies\Entities\Buddy;

interface BuddyRepository
{
    public function findById(int $id): Buddy;

    public function create(int $userId, BuddyData $data): Buddy;

    public function setData(Buddy $buddy, BuddyData $data): void;

    public function save(Buddy $buddy): void;
}
