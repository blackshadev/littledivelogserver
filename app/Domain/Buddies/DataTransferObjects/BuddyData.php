<?php

declare(strict_types=1);

namespace App\Domain\Buddies\DataTransferObjects;

class BuddyData
{
    private ?int $id = null;

    private ?string $name = null;

    private ?string $color = null;

    private ?string $email = null;

    public static function fromArray(array $data): self
    {
        $buddy = new self();
        $buddy->id = $data['buddy_id'] ?? null;
        $buddy->name = $data['name'] ?? $data['text'] ?? null;
        $buddy->color = $data['color'] ?? null;
        $buddy->email = $data['email'] ?? null;

        return $buddy;
    }

    public static function fromId(int $id)
    {
        $buddy = new self();
        $buddy->setId($id);
        return $buddy;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
