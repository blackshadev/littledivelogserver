<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\ViewModel;
use App\Domain\Tags\Entities\Tag;

final class DiveTagsViewModel extends ViewModel
{
    protected array $visible = ['tag_id', 'color', 'text'];

    public function __construct(
        private int $tagId,
        private string $color,
        private string $text,
    ) {
    }

    public static function fromTag(Tag $tag): self
    {
        return new self(
            tagId: $tag->getId(),
            color: $tag->getColor(),
            text: $tag->getText(),
        );
    }

    public function getTagId()
    {
        return $this->tagId;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getText()
    {
        return $this->text;
    }
}
