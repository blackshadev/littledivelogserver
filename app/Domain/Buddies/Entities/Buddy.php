<?php

declare(strict_types=1);

namespace App\Domain\Buddies\Entities;

use App\Domain\EntityWithId;

class Buddy implements EntityWithId
{
    private function __construct(
        private ?int $id,
        private int $userId,
        private string $name,
        private string $color,
        private ?string $email,
    ) {
    }

    public static function existing(
        int $id,
        int $userId,
        string $name,
        string $color,
        ?string $email,
    ): self {
        return new self($id, $userId, $name, $color, $email);
    }

    public static function new(
        int $userId,
        string $name,
        string $color,
        ?string $email,
    ): self {
        return new self(null, $userId, $name, $color, $email);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function isExisting(): bool
    {
        return $this->id !== null;
    }
}
