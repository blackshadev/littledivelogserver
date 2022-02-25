<?php

declare(strict_types=1);

namespace App\Domain\Dives\ValueObjects;

final class DiveId
{
    private function __construct(
        private ?int $diveId
    ) {
    }

    public function __toString(): string
    {
        return (string)$this->diveId;
    }

    public static function new(): self
    {
        return new self(null);
    }

    public static function existing(int $diveId): self
    {
        return new self($diveId);
    }

    public function isNew(): bool
    {
        return $this->diveId === null;
    }

    public function value(): int
    {
        return $this->diveId;
    }
}
