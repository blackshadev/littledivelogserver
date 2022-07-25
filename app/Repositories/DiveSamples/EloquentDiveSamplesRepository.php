<?php

declare(strict_types=1);

namespace App\Repositories\DiveSamples;

use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\DiveSamples\DiveSamplesRepository;
use App\Domain\DiveSamples\Entities\DiveSamples;
use App\Error\SaveOperationFailed;
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
        $affectedRows = Dive::query()
            ->where('id', $diveSamples->diveId())
            ->update(['samples' => $diveSamples->samples()]);

        if ($affectedRows !== 1) {
            throw SaveOperationFailed::singleRow($affectedRows);
        }
    }
}
