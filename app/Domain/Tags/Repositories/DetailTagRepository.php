<?php

declare(strict_types=1);

namespace App\Domain\Tags\Repositories;

use App\Domain\Tags\Entities\DetailTag;

interface DetailTagRepository
{
    public function findById(int $id): DetailTag;

    public function listForUser(int $userId): array;
}
