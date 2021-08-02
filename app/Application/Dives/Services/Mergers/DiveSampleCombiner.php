<?php

declare(strict_types=1);

namespace App\Application\Dives\Services\Mergers;

interface DiveSampleCombiner
{
    /**
     * @param Dive[] $dives
     * @return array[]
     */
    public function combine(array $dives): array;
}
