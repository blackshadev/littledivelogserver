<?php

declare(strict_types=1);

namespace App\Domain\Equipment\Entities;

use Webmozart\Assert\Assert;

final class Equipment
{
    /** @param Tank[] $tanks */
    private function __construct(
        private ?int $id,
        private int $userId,
        private array $tanks,
    ) {
        Assert::allIsInstanceOf($this->tanks, Tank::class);
    }

    public static function existing(
        int $id,
        int $userId,
        array $tanks,
    ): self {
        return new self($id, $userId, $tanks);
    }

    public static function new(
        int $userId,
        array $tanks,
    ): self {
        return new self(null, $userId, $tanks);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getTanks(): array
    {
        return $this->tanks;
    }

    public function setTanks(array $tanks): void
    {
        $this->tanks = $tanks;
    }

    public function isExisting(): bool
    {
        return $this->id !== null;
    }

    public function getTank(int $iX): ?Tank
    {
        return $this->tanks[$iX] ?? null;
    }
}
