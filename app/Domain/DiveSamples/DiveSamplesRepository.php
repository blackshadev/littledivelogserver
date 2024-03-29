<?php

declare(strict_types=1);

namespace App\Domain\DiveSamples;

use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\DiveSamples\Entities\DiveSamples;

interface DiveSamplesRepository
{
    public function findById(DiveId $diveId): DiveSamples;

    public function save(DiveSamples $diveSamples): void;
}
