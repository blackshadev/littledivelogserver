<?php

declare(strict_types=1);

namespace App\Domain\DiveSamples\Entities;

use Webmozart\Assert\Assert;

final class DiveSamplePressureAccessor
{
    private function __construct(private array &$raw)
    {
        Assert::keyExists($raw, 'Tank');
        Assert::keyExists($raw, 'Pressure');
    }

    public static function fromArray(array &$raw): self
    {
        return new self($raw);
    }

    public function tank(): int
    {
        return $this->raw['Tank'];
    }

    public function pressure(): float
    {
        return $this->raw['Pressure'];
    }

    public function toArray(): array
    {
        return $this->raw;
    }
}
