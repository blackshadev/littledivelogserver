<?php

declare(strict_types=1);

namespace App\Domain\DiveSamples\Services;

use App\Domain\DiveSamples\DiveSamplesRepository;
use App\Domain\DiveSamples\Entities\DiveSamples;
use App\Domain\DiveSamples\ValueObjects\DiveSamplesFixerResult;
use App\Domain\DiveSamples\Visitors\DivePressureUniqueifier;

final class FixDiveSamplePressures
{
    public function __construct(private readonly DiveSamplesRepository $diveSamplesRepository)
    {
    }

    public function fix(DiveSamples $diveSamples): DiveSamplesFixerResult
    {
        $visitor = new DivePressureUniqueifier();
        $fixedDiveSamples = $diveSamples->accept($visitor);

        if (!$visitor->hasUpdatedSamples()) {
            return DiveSamplesFixerResult::untouched($diveSamples);
        }

        $this->diveSamplesRepository->save($fixedDiveSamples);
        return DiveSamplesFixerResult::touched($fixedDiveSamples);
    }
}
