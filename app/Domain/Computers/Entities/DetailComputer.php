<?php

declare(strict_types=1);

namespace App\Domain\Computers\Entities;

use DateTimeInterface;

class DetailComputer
{
    public function __construct(
        private ?int $computerId,
        private int $serial,
        private string $vendor,
        private int $model,
        private int $type,
        private string $name,
        private int $diveCounts,
        private ?DateTimeInterface $lastRead,
        private ?string $lastFingerprint,
    ) {
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function getModel(): int
    {
        return $this->model;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSerial(): int
    {
        return $this->serial;
    }

    public function getComputerId(): ?int
    {
        return $this->computerId;
    }

    public function getDiveCounts(): int
    {
        return $this->diveCounts;
    }

    public function getLastRead(): ?DateTimeInterface
    {
        return $this->lastRead;
    }

    public function getLastFingerprint(): ?string
    {
        return $this->lastFingerprint;
    }
}
