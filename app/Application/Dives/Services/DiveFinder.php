<?php

declare(strict_types=1);

namespace App\Application\Dives\Services;

use App\Application\Dives\CommandObjects\FindDivesCommand;
use App\Domain\Dives\Entities\DiveSummary;

interface DiveFinder
{
    /** @return DiveSummary[] */
    public function search(FindDivesCommand $findDivesCommand): array;
}
