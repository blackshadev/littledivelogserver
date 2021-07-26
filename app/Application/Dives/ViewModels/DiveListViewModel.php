<?php

declare(strict_types=1);

namespace App\Application\Dives\ViewModels;

use App\Application\Places\ViewModels\PlaceViewModel;
use App\Application\ViewModels\ApiModels\DiveTagsViewModel;
use App\Application\ViewModels\ViewModel;
use App\Domain\Dives\Entities\DiveSummary;
use App\Domain\Places\Entities\Place;
use App\Domain\Support\Arrg;
use App\Domain\Tags\Entities\Tag;
use DateTimeInterface;
use Webmozart\Assert\Assert;

final class DiveListViewModel extends ViewModel
{
    protected array $visible = ['dive_id', 'divetime', 'date', 'tags', 'place'];

    public function __construct(
        private int $diveId,
        private ?int $divetime,
        private ?DateTimeInterface $date,
        private array $tags,
        private ?Place $place,
    ) {
        Assert::allIsInstanceOf($tags, Tag::class);
    }

    public static function fromDiveSummary(DiveSummary $diveSummary): self
    {
        return new self(
            diveId: $diveSummary->getDiveId(),
            divetime: $diveSummary->getDivetime(),
            date: $diveSummary->getDate(),
            place: $diveSummary->getPlace(),
            tags: $diveSummary->getTags(),
        );
    }

    public function getDiveId(): int
    {
        return $this->diveId;
    }

    public function getDivetime(): ?int
    {
        return $this->divetime;
    }

    public function getDate(): ?string
    {
        return $this->date !== null ? $this->date->format(DATE_ATOM) : null;
    }

    public function getTags(): array
    {
        return Arrg::map($this->tags, fn (Tag $tag) => DiveTagsViewModel::fromTag($tag));
    }

    public function getPlace(): ?PlaceViewModel
    {
        return $this->place ? PlaceViewModel::fromPlace($this->place) : null;
    }
}
