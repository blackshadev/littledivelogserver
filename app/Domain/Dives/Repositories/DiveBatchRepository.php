<?php

declare(strict_types=1);

namespace App\Domain\Dives\Repositories;

use App\Domain\Dives\Entities\Dive;

interface DiveBatchRepository
{
    /**
     * @param int[] $diveIds
     * @return Dive[]
     */
    public function findByIds(array $diveIds): array;

    /**
     * @param Dive[] $divesToReplace
     * @param Dive $newDive
     */
    public function replace(array $divesToReplace, Dive $newDive): void;
}
