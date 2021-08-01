<?php

declare(strict_types=1);

namespace App\Domain\Tags\Entities;

use App\Domain\EntityWithId;

final class Tag implements EntityWithId
{
    public function __construct(
        private ?int $id,
        private int $userId,
        private string $text,
        private string $color,
    ) {
    }

    public static function existing(
        int $id,
        int $userId,
        string $text,
        string $color,
    ): self {
        return new self($id, $userId, $text, $color);
    }

    public static function new(
        int $userId,
        string $text,
        string $color,
    ): self {
        return new self(null, $userId, $text, $color);
    }

    public function getId(): int
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

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function isExisting()
    {
        return $this->id !== null;
    }
}
