<?php

declare(strict_types=1);

namespace App\Application\Dives\Services\Mergers;

use App\Domain\Dives\Entities\DiveTank;

interface DiveTankMerger
{
    /**
     * @param Dive[] $dives
     * @return DiveTank[]
     */
    public function mergeForDives(array $dives): array;

    /**
     * @param DiveTank[] $tanks
     */
    public function merge(array $tanks): ?DiveTank;
}
