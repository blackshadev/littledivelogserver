<?php

declare(strict_types=1);

namespace App\Domain\Dives\Entities;

use App\Domain\Places\Entities\Place;
use App\Domain\Tags\Entities\Tag;
use DateTimeInterface;
use Webmozart\Assert\Assert;

final class DiveSummary
{
    public function __construct(
        private int $diveId,
        private ?int $divetime,
        private ?DateTimeInterface $date,
        private array $tags,
        private ?Place $place,
    ) {
        Assert::allIsInstanceOf($tags, Tag::class);
    }

    public function getDiveId(): int
    {
        return $this->diveId;
    }

    public function getDivetime(): ?int
    {
        return $this->divetime;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }
}
