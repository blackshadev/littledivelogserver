<?php

declare(strict_types=1);

namespace App\Domain\Buddies\Repositories;

use App\Domain\Buddies\Entities\DetailBuddy;

interface DetailBuddyRepository
{
    public function findById(int $id): DetailBuddy;

    /** @return DetailBuddy[] */
    public function listForUser(int $userId): array;
}
