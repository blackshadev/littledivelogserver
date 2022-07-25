<?php

declare(strict_types=1);

namespace App\Domain\DiveSamples\Entities;

use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\DiveSamples\Visitors\DiveSampleVisitor;

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

    public function accept(DiveSampleVisitor $diveSampleVisitor): DiveSamples
    {
        /** @var mixed[] $newSamples */
        $newSamples = [];
        foreach ($this->samples as &$sample) {
            $newSamples[] = $diveSampleVisitor->visit(DiveSampleAccessor::fromArray($sample));
        }

        return DiveSamples::create($this->diveId, $newSamples);
    }
}
