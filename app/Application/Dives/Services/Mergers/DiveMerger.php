<?php

declare(strict_types=1);

namespace App\Application\Dives\Services\Mergers;

use App\Domain\Dives\Entities\Dive;

interface DiveMerger
{
    /**
     * @param Dive[] $dives
     */
    public function merge(array $dives): Dive;
}
