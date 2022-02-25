<?php

declare(strict_types=1);

namespace App\Application\Buddies\ViewModels;

use App\Application\ViewModels\ViewModel;
use App\Domain\Buddies\Entities\DetailBuddy;
use DateTimeInterface;

final class BuddyDetailViewModel extends ViewModel
{
    protected array $visible = [
        'buddy_id', 'text', 'color', 'dive_count', 'last_dive', 'email', 'buddy_user_id', 'updated'
    ];

    public function __construct(
        private int $buddyId,
        private string $text,
        private string $color,
        private ?string $email,
        private int $buddyUserId,
        private int $diveCount,
        private ?DateTimeInterface $lastDive,
        private DateTimeInterface $updated,
    ) {
    }

    public static function fromDetailBuddy(DetailBuddy $buddy): self
    {
        return new self(
            buddyId: $buddy->getId(),
            text: $buddy->getName(),
            color: $buddy->getColor(),
            email: $buddy->getEmail(),
            buddyUserId: 0,
            diveCount: $buddy->getDiveCount(),
            lastDive: $buddy->getLastDive(),
            updated: $buddy->getUpdated(),
        );
    }

    public function getBuddyId(): int
    {
        return $this->buddyId;
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
        return $this->lastDive !== null ? $this->lastDive->format(DateTimeInterface::ATOM) : null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getBuddyUserId(): ?int
    {
        return $this->buddyUserId;
    }

    public function getUpdated(): string
    {
        return $this->updated->format(\DateTimeInterface::ATOM);
    }
}
