<?php

declare(strict_types=1);

namespace App\Domain\Computers\Entities;

use DateTimeInterface;

class Computer
{
    public function __construct(
        private ?int $computerId,
        private int $userId,
        private int $serial,
        private string $vendor,
        private int $model,
        private int $type,
        private string $name,
        private ?DateTimeInterface $lastRead = null,
        private ?string $fingerprint = null
    ) {
    }

    public static function new(
        int $userId,
        int $serial,
        string $vendor,
        int $model,
        int $type,
        string $name,
    ): self {
        return new self(
            computerId: null,
            userId: $userId,
            serial: $serial,
            vendor: $vendor,
            model: $model,
            type: $type,
            name: $name,
        );
    }

    public static function existing(
        int $userId,
        int $computerId,
        int $serial,
        string $vendor,
        int $model,
        int $type,
        string $name,
    ): self {
        return new self(
            userId: $userId,
            computerId: $computerId,
            serial: $serial,
            vendor: $vendor,
            model: $model,
            type: $type,
            name: $name,
        );
    }

    public function getComputerId(): ?int
    {
        return $this->computerId;
    }

    public function setComputerId(?int $computerId): void
    {
        $this->computerId = $computerId;
    }

    public function getLastRead(): ?DateTimeInterface
    {
        return $this->lastRead;
    }

    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getId(): ?int
    {
        return $this->computerId;
    }

    public function setId(int $computerId): void
    {
        $this->computerId = $computerId;
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

    public function setSerial(int $serial): void
    {
        $this->serial = $serial;
    }

    public function setVendor(string $vendor): void
    {
        $this->vendor = $vendor;
    }

    public function setModel(int $model): void
    {
        $this->model = $model;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isExisting(): bool
    {
        return $this->computerId !== null;
    }

    public function updateLastRead(DateTimeInterface $date, string $fingerprint)
    {
        if ($this->lastRead !== null && $this->lastRead > $date) {
            return;
        }

        $this->lastRead = $date;
        $this->fingerprint = $fingerprint;
    }
}
