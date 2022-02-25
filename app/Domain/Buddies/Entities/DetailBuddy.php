<?php

declare(strict_types=1);

namespace App\Domain\Buddies\Entities;

use DateTimeInterface;

final class DetailBuddy
{
    public function __construct(
        private ?int $id,
        private int $userId,
        private string $name,
        private string $color,
        private ?string $email,
        private ?DateTimeInterface $lastDive,
        private int $diveCount,
        private ?DateTimeInterface $updated,
    ) {
    }

    public function getLastDive(): ?DateTimeInterface
    {
        return $this->lastDive;
    }

    public function getDiveCount(): int
    {
        return $this->diveCount;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUpdated(): DateTimeInterface
    {
        return $this->updated;
    }
}
