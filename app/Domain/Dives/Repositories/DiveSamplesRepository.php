<?php

declare(strict_types=1);

namespace App\Domain\Dives\Repositories;

use App\Domain\Dives\Entities\DiveSamples;
use App\Domain\Dives\ValueObjects\DiveId;

interface DiveSamplesRepository
{
    public function findById(DiveId $diveId): DiveSamples;

    public function save(DiveSamples $diveSamples): void;
}
