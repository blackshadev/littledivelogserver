<?php

declare(strict_types=1);

namespace App\Domain\Users\Entities;

class DetailUser
{
    public function __construct(
        private int $userId,
        private string $name,
        private string $email,
        private \DateTimeInterface $inserted,
        private int $tagCount,
        private int $buddyCount,
        private int $computerCount,
        private int $diveCount,
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getInserted(): \DateTimeInterface
    {
        return $this->inserted;
    }

    public function getTagCount(): int
    {
        return $this->tagCount;
    }

    public function getBuddyCount(): int
    {
        return $this->buddyCount;
    }

    public function getComputerCount(): int
    {
        return $this->computerCount;
    }

    public function getDiveCount(): int
    {
        return $this->diveCount;
    }
}
