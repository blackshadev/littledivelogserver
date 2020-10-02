<?php

namespace App\DataTransferObjects;

class BuddyData
{
    private ?int $id;
    private ?string $name;
    private ?string $color;

    public static function fromArray(array $data): self
    {
        $buddy = new BuddyData();
        $buddy->id = $data['buddy_id'] ?? null;
        $buddy->name = $data['name'] ?? null;
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

}
