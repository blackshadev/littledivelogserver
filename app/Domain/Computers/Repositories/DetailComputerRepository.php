<?php

declare(strict_types=1);

namespace App\Domain\Computers\Repositories;

use App\Domain\Computers\Entities\DetailComputer;

interface DetailComputerRepository
{
    /** @return DetailComputer */
    public function listForUser(int $userId): array;

    public function findById(int $computerId): DetailComputer;
}
