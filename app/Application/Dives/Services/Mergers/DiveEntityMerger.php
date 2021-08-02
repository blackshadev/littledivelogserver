<?php

declare(strict_types=1);

namespace App\Application\Dives\Services\Mergers;

interface DiveEntityMerger
{
    public function unique(array $entities): array;
}
