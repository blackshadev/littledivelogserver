<?php

namespace App\DataTransferObjects;

use Carbon\Carbon;

class DiveData
{
    private ?Carbon $date;
    private ?int $divetime;
    private ?float $maxDepth;
    private array $tags = [];
    private array $buddies = [];
    private PlaceData $place;
    private array $tanks;

    public static function fromArray(array $data): self
    {
        $diveData = new DiveData();

        $diveData->date = Carbon::parse($data['date']) ?? null;
        $diveData->divetime = $data['divetime'] ?? null;
        $diveData->maxDepth = $data['max_depth'] ?? null;
        $diveData->tags = array_map(fn ($tagData) => TagData::fromArray($tagData), $data['tags'] ?? []);
        $diveData->buddies = array_map(fn ($buddyData) => BuddyData::fromArray($buddyData), $data['buddies'] ?? []);
        $diveData->tanks = array_map(fn ($tank) => TankData::fromArray($tank), $data['tanks'] ?? []);
        $diveData->place = PlaceData::fromArray($data['place']);

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
