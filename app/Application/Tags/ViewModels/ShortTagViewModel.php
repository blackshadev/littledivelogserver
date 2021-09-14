<?php

declare(strict_types=1);

namespace App\Application\Tags\ViewModels;

use App\Application\ViewModels\ViewModel;
use App\Domain\Tags\Entities\Tag;

final class ShortTagViewModel extends ViewModel
{
    protected array $visible = ['tag_id', 'text', 'color'];

    public function __construct(
        private int $id,
        private string $text,
        private string $color,
    ) {
    }

    public static function fromTag(Tag $tag): self
    {
        return new self($tag->getId(), $tag->getText(), $tag->getColor());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getColor(): string
    {
        return $this->color;
    }
}
