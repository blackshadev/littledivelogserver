<?php

declare(strict_types=1);

namespace App\Application\Buddies\ViewModels;

use App\Application\ViewModels\ViewModel;
use App\Domain\Buddies\Entities\DetailBuddy;
use DateTimeInterface;

class BuddyListViewModel extends ViewModel
{
    protected array $visible = ['buddy_id', 'text', 'color', 'dive_count', 'last_dive'];

    public function __construct(
        private int $buddyId,
        private string $text,
        private string $color,
        private int $diveCount,
        private ?DateTimeInterface $lastDive,
    ) {
    }

    public static function fromDetailBuddy(DetailBuddy $buddy): self
    {
        return new self(
            buddyId: $buddy->getId(),
            text: $buddy->getName(),
            color: $buddy->getColor(),
            lastDive:$buddy->getLastDive(),
            diveCount: $buddy->getDiveCount(),
        );
    }

    public function getBuddyId()
    {
        return $this->buddyId;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getDiveCount()
    {
        return $this->diveCount;
    }

    public function getLastDive()
    {
        return $this->lastDive;
    }
}
