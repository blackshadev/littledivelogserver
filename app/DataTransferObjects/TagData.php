<?php

namespace App\DataTransferObjects;

class TagData
{
    private ?int $id;
    private ?string $text;
    private ?string $color;

    public static function fromArray(array $data): self
    {
        $tagData = new TagData();
        $tagData->id = $data['tag_id'] ?? null;
        $tagData->text = $data['text'] ?? null;
        $tagData->color = $data['color'] ?? null;
        return $tagData;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }
}
