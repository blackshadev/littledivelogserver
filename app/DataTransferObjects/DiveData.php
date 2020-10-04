<?php

namespace App\DataTransferObjects;

use Carbon\Carbon;

class DiveData
{
    private ?Carbon $date;
    private ?int $divetime;
    private ?float $maxDepth;
    private ?int $computerId;
    private ?string $fingerprint;
    private PlaceData $place;
    private array $tanks;
    private array $tags;
    private array $buddies;

    public static function fromArray(array $data): self
    {
        $diveData = new self();

        $diveData->date = Carbon::parse($data['date']) ?? null;
        $diveData->divetime = $data['divetime'] ?? null;
        $diveData->maxDepth = $data['max_depth'] ?? null;
        $diveData->computerId = $data['computer_id'] ?? null;
        $diveData->fingerprint = $data['fingerprint'] ?? null;
        $diveData->place = PlaceData::fromArray($data['place']);
        $diveData->tags = array_map(fn ($tagData) => TagData::fromArray($tagData), $data['tags'] ?? []);
        $diveData->buddies = array_map(fn ($buddyData) => BuddyData::fromArray($buddyData), $data['buddies'] ?? []);
        $diveData->tanks = array_map(fn ($tank) => TankData::fromArray($tank), $data['tanks'] ?? []);

        return $diveData;
    }

    function getDate(): ?Carbon
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

    /** @return TagData[] */
    public function getTags(): array
    {
        return $this->tags;
    }

    /** @return BuddyData[] */
    public function getBuddies(): array
    {
        return $this->buddies;
    }

    /** @return TankData[] */
    public function getTanks(): array
    {
        return $this->tanks;
    }

}
