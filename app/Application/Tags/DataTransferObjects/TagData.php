<?php

declare(strict_types=1);

namespace App\Application\Tags\DataTransferObjects;

class TagData
{
    private ?int $id = null;

    private ?string $text = null;

    private ?string $color = null;

    public static function fromArray(array $data): self
    {
        $tagData = new self();
        $tagData->id = $data['tag_id'] ?? null;
        $tagData->text = $data['text'] ?? null;
        $tagData->color = $data['color'] ?? null;

        return $tagData;
    }

    public static function fromId(int $id)
    {
        $tag = new self();
        $tag->setId($id);
        return $tag;
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

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }
}
