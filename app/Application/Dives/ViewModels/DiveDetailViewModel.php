<?php

declare(strict_types=1);

namespace App\Application\Dives\ViewModels;

use App\Application\Buddies\ViewModels\ShortBuddyViewModel;
use App\Application\Places\ViewModels\PlaceViewModel;
use App\Application\Tags\ViewModels\ShortTagViewModel;
use App\Application\ViewModels\ViewModel;
use App\Domain\Buddies\Entities\Buddy;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Entities\DiveTank;
use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\Support\Arrg;
use App\Domain\Tags\Entities\Tag;

final class DiveDetailViewModel extends ViewModel
{
    protected array $visible = [
        'dive_id', 'date', 'divetime',
        'max_depth', 'place', 'buddies',
        'tags', 'tanks', 'updated', 'computer'
    ];

    /**
     * @param ShortTagViewModel[] $tags
     * @param ShortBuddyViewModel[] $buddies
     * @param DiveTankViewModel[] $diveTanks
     */
    public function __construct(
        private DiveId $diveId,
        private ?\DateTimeInterface $date,
        private ?int $divetime,
        private ?float $maxDepth,
        private ?PlaceViewModel $place,
        private ?ComputerDiveViewModel $computer,
        private array $tags,
        private array $buddies,
        private array $diveTanks,
        private \DateTimeInterface $updated,
    ) {
    }

    public static function fromDive(Dive $dive): self
    {
        return new self(
            $dive->getDiveId(),
            $dive->getDate(),
            $dive->getDivetime(),
            $dive->getMaxDepth(),
            $dive->getPlace() !== null ? PlaceViewModel::fromPlace($dive->getPlace()) : null,
            $dive->getComputer() !== null ? ComputerDiveViewModel::fromComputer($dive->getComputer()) : null,
            Arrg::map($dive->getTags(), fn (Tag $tag) => ShortTagViewModel::fromTag($tag)),
            Arrg::map($dive->getBuddies(), fn (Buddy $buddy) => ShortBuddyViewModel::fromBuddy($buddy)),
            Arrg::map($dive->getTanks(), fn (DiveTank $diveTank) => DiveTankViewModel::fromDiveTank($diveTank)),
            $dive->getUpdated(),
        );
    }

    public function getDiveId()
    {
        return $this->diveId->value();
    }

    public function getDivetime(): ?int
    {
        return $this->divetime;
    }

    public function getDate(): ?string
    {
        return $this->date !== null ? $this->date->format(\DateTimeInterface::ATOM) : null;
    }

    public function getMaxDepth(): ?float
    {
        return (float)$this->maxDepth;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getBuddies(): array
    {
        return $this->buddies;
    }

    public function getPlace(): ?PlaceViewModel
    {
        return $this->place;
    }

    public function getTanks(): array
    {
        return $this->diveTanks;
    }

    public function getComputer(): ?ComputerDiveViewModel
    {
        return $this->computer;
    }

    public function getSamples(): array
    {
        return $this->samples;
    }

    public function getUpdated(): string
    {
        return $this->updated->format(\DateTimeInterface::ATOM);
    }
}
