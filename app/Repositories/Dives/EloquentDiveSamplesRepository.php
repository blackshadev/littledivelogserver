<?php

declare(strict_types=1);

namespace App\Repositories\Dives;

use App\Domain\Dives\Entities\DiveSamples;
use App\Domain\Dives\Repositories\DiveSamplesRepository;
use App\Domain\Dives\ValueObjects\DiveId;
use App\Models\Dive;

final class EloquentDiveSamplesRepository implements DiveSamplesRepository
{
    public function findById(DiveId $diveId): DiveSamples
    {
        $diveSamples = Dive::query()->select(['samples'])->findOrFail($diveId->value())->samples;
        return DiveSamples::create($diveId, $diveSamples);
    }

    public function save(DiveSamples $diveSamples): void
    {
        // TODO: Implement save() method.
    }
}
