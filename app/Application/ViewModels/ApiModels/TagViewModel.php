<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\ViewModel;
use App\Domain\Tags\Entities\DetailTag;
use DateTimeInterface;

class TagViewModel extends ViewModel
{
    protected array $visible = ['tag_id', 'text', 'color', 'dive_count', 'last_dive'];

    public function __construct(
        private int $tagId,
        private string $text,
        private string $color,
        private int $diveCount,
        private ?DateTimeInterface $lastDive,
    ) {
    }

    public static function fromDetailTag(DetailTag $tag)
    {
        return new self(
            tagId: $tag->getId(),
            text: $tag->getText(),
            color: $tag->getColor(),
            diveCount: $tag->getDiveCount(),
            lastDive: $tag->getLastDive(),
        );
    }

    public function getTagId(): int
    {
        return $this->tagId;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getDiveCount(): int
    {
        return $this->diveCount;
    }

    public function getLastDive(): ?string
    {
        return $this->lastDive !== null ? $this->lastDive->format(\DateTimeInterface::ATOM) : null;
    }
}
