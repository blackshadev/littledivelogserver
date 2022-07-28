<?php

declare(strict_types=1);

namespace App\Domain\DiveSamples\ValueObjects;

use App\Domain\DiveSamples\Entities\DiveSamples;

final class DiveSamplesFixerResult
{
    private function __construct(public readonly bool $touched, public readonly DiveSamples $result)
    {
    }

    public static function touched(DiveSamples $diveSamples): self
    {
        return new self(true, $diveSamples);
    }

    public static function untouched(DiveSamples $diveSamples): self
    {
        return new self(false, $diveSamples);
    }
}
