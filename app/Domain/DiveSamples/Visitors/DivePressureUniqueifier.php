<?php

declare(strict_types=1);

namespace App\Domain\DiveSamples\Visitors;

use App\Domain\DiveSamples\Entities\DiveSampleAccessor;
use App\Domain\DiveSamples\Entities\DiveSamplePressureAccessor;
use App\Domain\DiveSamples\Entities\Field;

final class DivePressureUniqueifier implements DiveSampleVisitor
{
    private bool $hasUpdatedDive = false;

    public function visit(DiveSampleAccessor $diveSamples): array
    {
        if (!$diveSamples->has(Field::Pressure)) {
            return $diveSamples->toArray();
        }

        $pressures = [...$diveSamples->pressures()];
        $uniquePressures = $this->uniqueifyPressures($pressures);

        if (count($pressures) === count($uniquePressures)) {
            return $diveSamples->toArray();
        }

        $pressuresAsArray = array_map(
            static fn (DiveSamplePressureAccessor $pressure) => $pressure->toArray(),
            $uniquePressures
        );

        $this->hasUpdatedDive = true;
        return $diveSamples->with([
            Field::Pressure->value => $pressuresAsArray
        ]);
    }

    public function hasUpdatedSamples(): bool
    {
        return $this->hasUpdatedDive;
    }

    /**
     * @param iterable<DiveSamplePressureAccessor> $pressures
     * @return DiveSamplePressureAccessor[]
     */
    private function uniqueifyPressures(iterable $pressures): array
    {
        $pressuresPerTank = [];
        foreach ($pressures as $pressure) {
            $pressuresPerTank[$pressure->tank()] = $pressure;
        }

        return array_values($pressuresPerTank);
    }
}
