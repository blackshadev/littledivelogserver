<?php

declare(strict_types=1);

namespace App\Domain\Dives\Repositories;

use App\Domain\Dives\Entities\DiveSummary;
use App\Domain\Users\Entities\User;

interface DiveSummaryRepository
{
    /** @return DiveSummary[] */
    public function listForUser(User $user): array;

    /** @return DiveSummary[] */
    public function findByIds(array $ids): array;
}
