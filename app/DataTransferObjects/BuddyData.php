<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

class BuddyData
{
    private ?int $id = null;

    private ?string $name = null;

    private ?string $color = null;

    public static function fromArray(array $data): self
    {
        $buddy = new self();
        $buddy->id = $data['buddy_id'] ?? null;
        $buddy->name = $data['name'] ?? $data['text'] ?? null;
        $buddy->color = $data['color'] ?? null;

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
}
