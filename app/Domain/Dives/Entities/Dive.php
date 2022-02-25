<?php

declare(strict_types=1);

namespace App\Domain\Dives\Entities;

use App\Domain\Buddies\Entities\Buddy;
use App\Domain\Computers\Entities\Computer;
use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\Places\Entities\Place;
use App\Domain\Tags\Entities\Tag;
use DateTimeInterface;
use Webmozart\Assert\Assert;

final class Dive
{
    private function __construct(
        private DiveId $diveId,
        private ?int $userId,
        private DateTimeInterface $updated,
        private ?DateTimeInterface $date,
        private ?int $divetime,
        private ?float $maxDepth,
        private ?Computer $computer,
        private ?string $fingerprint,
        private ?Place $place,
        private array $tanks,
        private array $tags,
        private array $buddies,
        private ?array $samples
    ) {
        Assert::allIsInstanceOf($tanks, DiveTank::class);
        Assert::allIsInstanceOf($tags, Tag::class);
        Assert::allIsInstanceOf($buddies, Buddy::class);
    }

    public static function existing(
        DiveId $diveId,
        int $userId,
        DateTimeInterface $updated,
        ?DateTimeInterface $date,
        ?int $divetime = null,
        ?float $maxDepth = null,
        ?Computer $computer = null,
        ?string $fingerprint = null,
        ?Place $place = null,
        array $tanks = [],
        array $tags = [],
        array $buddies = [],
        ?array $samples = null
    ): self {
        return new static(
            diveId: $diveId,
            userId: $userId,
            updated: $updated,
            date: $date,
            divetime: $divetime,
            maxDepth: $maxDepth,
            computer: $computer,
            fingerprint: $fingerprint,
            place: $place,
            tanks: $tanks,
            tags: $tags,
            buddies: $buddies,
            samples: $samples
        );
    }

    public static function new(
        ?int $userId,
        ?DateTimeInterface $date,
        ?int $divetime = null,
        ?float $maxDepth = null,
        ?Computer $computer = null,
        ?string $fingerprint = null,
        ?Place $place = null,
        array $tanks = [],
        array $tags = [],
        array $buddies = [],
        ?array $samples = null,
    ): self {
        return new self(
            diveId: DiveId::new(),
            userId: $userId,
            updated: new \DateTimeImmutable(),
            date: $date,
            divetime: $divetime,
            maxDepth: $maxDepth,
            computer: $computer,
            fingerprint: $fingerprint,
            place: $place,
            tanks: $tanks,
            tags: $tags,
            buddies: $buddies,
            samples: $samples
        );
    }

    public function getDiveId(): DiveId
    {
        return $this->diveId;
    }

    public function setDiveId(DiveId $diveId): void
    {
        $this->diveId = $diveId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getDivetime(): ?int
    {
        return $this->divetime;
    }

    public function setDivetime(?int $divetime): void
    {
        $this->divetime = $divetime;
    }

    public function getMaxDepth(): ?float
    {
        return $this->maxDepth;
    }

    public function setMaxDepth(?float $maxDepth): void
    {
        $this->maxDepth = $maxDepth;
    }

    public function getComputer(): ?Computer
    {
        return $this->computer;
    }

    public function setComputer(?Computer $computerId): void
    {
        $this->computer = $computerId;
    }

    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    public function setFingerprint(?string $fingerprint): void
    {
        $this->fingerprint = $fingerprint;
    }

    /** @return Place|null */
    public function getPlace(): ?Place
    {
        return $this->place;
    }

    /** @param Place|null $place */
    public function setPlace(?Place $place): void
    {
        $this->place = $place;
    }

    /** @return DiveTank[] */
    public function getTanks(): array
    {
        return $this->tanks;
    }

    /** @param DiveTank[] $tanks */
    public function setTanks(array $tanks): void
    {
        $this->tanks = $tanks;
    }

    /** @return Tag[] */
    public function getTags(): array
    {
        return $this->tags;
    }

    /** @param Tag[] $tags */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /** @return Buddy[] */
    public function getBuddies(): array
    {
        return $this->buddies;
    }

    /** @param Buddy[] $buddies */
    public function setBuddies(array $buddies): void
    {
        $this->buddies = $buddies;
    }

    public function isExisting(): bool
    {
        return !$this->diveId->isNew();
    }

    public function getUpdated(): DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(DateTimeInterface $updated): void
    {
        $this->updated = $updated;
    }

    public function getSamples(): array
    {
        return $this->samples;
    }

    public function setSamples(array $samples): void
    {
        $this->samples = $samples;
    }

    public function hasSamples(): bool
    {
        return !empty($this->samples);
    }
}
