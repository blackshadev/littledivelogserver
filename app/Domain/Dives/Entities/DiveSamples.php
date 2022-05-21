<?php

declare(strict_types=1);

namespace App\Domain\Dives\Entities;

use App\Domain\Dives\ValueObjects\DiveId;

final class DiveSamples
{
    public function __construct(
        private DiveId $diveId,
        private array $samples = [],
    ) {
    }

    public static function create(DiveId $diveId, mixed $samples)
    {
        return new self($diveId, $samples ?? []);
    }

    public function samples(): array
    {
        return $this->samples;
    }

    public function diveId(): DiveId
    {
        return $this->diveId;
    }
}
