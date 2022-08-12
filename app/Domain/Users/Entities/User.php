<?php

declare(strict_types=1);

namespace App\Domain\Users\Entities;

final class User
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isExisting(): bool
    {
        return $this->id !== null;
    }
}
