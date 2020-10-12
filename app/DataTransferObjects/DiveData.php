<?php

namespace App\DataTransferObjects;

use Carbon\Carbon;

class DiveData
{
    private ?Carbon $date = null;
    private ?int $divetime = null;
    private ?float $maxDepth = null;
    private ?int $computerId = null;
    private ?string $fingerprint = null;
    private PlaceData $place;
    private ?array $tanks = null;
    private ?array $tags = null;
    private ?array $buddies = null;

    public function __construct()
    {
        $this->place = new PlaceData();
    }

    public static function fromArray(array $data): self
    {
        $diveData = new self();

        $diveData->date = Carbon::parse($data['date']) ?? null;
        $diveData->divetime = $data['divetime'] ?? null;
        $diveData->maxDepth = $data['max_depth'] ?? null;
        $diveData->computerId = $data['computer_id'] ?? null;
        $diveData->fingerprint = $data['fingerprint'] ?? null;
        $diveData->place = PlaceData::fromArray($data['place'] ?? []);
        $diveData->tags = array_map(fn ($tagData) => TagData::fromArray($tagData), $data['tags'] ?? []);
        $diveData->buddies = array_map(fn ($buddyData) => BuddyData::fromArray($buddyData), $data['buddies'] ?? []);
        $diveData->tanks = array_map(fn ($tank) => TankData::fromArray($tank), $data['tanks'] ?? []);

        return $diveData;
    }

    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    public function getDivetime(): ?int
    {
        return $this->divetime;
    }

    public function getMaxDepth(): ?float
    {
        return $this->maxDepth;
    }

    public function getComputerId(): ?int
    {
        return $this->computerId;
    }

    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    public function getPlace(): PlaceData
    {
        return $this->place;
    }

    /** @return null|TagData[] */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /** @return null|BuddyData[] */
    public function getBuddies(): ?array
    {
        return $this->buddies;
    }

    /** @return null|TankData[] */
    public function getTanks(): ?array
    {
        return $this->tanks;
    }

    public function setDate(?Carbon $date): void
    {
        $this->date = $date;
    }

    public function setDivetime(?int $divetime): void
    {
        $this->divetime = $divetime;
    }

    public function setMaxDepth(?float $maxDepth): void
    {
        $this->maxDepth = $maxDepth;
    }

    public function setComputerId(?int $computerId): void
    {
        $this->computerId = $computerId;
    }

    public function setFingerprint(?string $fingerprint): void
    {
        $this->fingerprint = $fingerprint;
    }

    public function setPlace(PlaceData $place): void
    {
        $this->place = $place;
    }

    public function setTanks(?array $tanks): void
    {
        $this->tanks = $tanks;
    }

    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    public function setBuddies(?array $buddies): void
    {
        $this->buddies = $buddies;
    }
}
