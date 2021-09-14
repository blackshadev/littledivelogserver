<?php

declare(strict_types=1);

namespace App\Application\Buddies\ViewModels;

use App\Application\ViewModels\ViewModel;
use App\Domain\Buddies\Entities\Buddy;

final class ShortBuddyViewModel extends ViewModel
{
    protected array $visible = ['buddy_id', 'text', 'color'];

    public function __construct(
        private int $id,
        private string $text,
        private string $color
    ) {
    }

    public static function fromBuddy(Buddy $buddy): self
    {
        return new self($buddy->getId(), $buddy->getName(), $buddy->getColor());
    }

    public function getBuddyId(): int
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
