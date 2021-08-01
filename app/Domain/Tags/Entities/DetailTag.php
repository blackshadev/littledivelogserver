<?php

declare(strict_types=1);

namespace App\Domain\Tags\Entities;

use DateTimeInterface;

final class DetailTag
{
    public function __construct(
        private ?int $id,
        private int $userId,
        private string $text,
        private string $color,
        private ?DateTimeInterface $lastDive,
        private int $diveCount,
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
