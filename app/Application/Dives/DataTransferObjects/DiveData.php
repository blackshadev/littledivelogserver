<?php

declare(strict_types=1);

namespace App\Application\Dives\DataTransferObjects;

use App\Application\Buddies\DataTransferObjects\BuddyData;
use App\Application\Equipment\DataTransferObjects\TankData;
use App\Application\Places\DataTransferObjects\PlaceData;
use App\Application\Tags\DataTransferObjects\TagData;
use Carbon\Carbon;

final class DiveData
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

    private ?array $samples = null;

    public function __construct()
    {
        $this->place = new PlaceData();
    }

    public static function fromArray(array $data): self
    {
        $diveData = new static();
        $diveData->setData($data);
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

    public function getSamples(): ?array
    {
        return $this->samples;
    }

    public function setSamples(?array $samples): void
    {
        $this->samples = $samples;
    }

    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    public function setBuddies(?array $buddies): void
    {
        $this->buddies = $buddies;
    }

    private function setData(array $data): void
    {
        $this->date = Carbon::parse($data['date']) ?? null;
        $this->divetime = $data['divetime'] ?? null;
        $this->maxDepth = $data['max_depth'] ?? null;
        $this->computerId = $data['computer_id'] ?? null;
        $this->fingerprint = $data['fingerprint'] ?? null;
        $this->samples = $data['samples'] ?? null;
        $this->place = PlaceData::fromArray($data['place'] ?? []);
        $this->tags = array_map(fn ($tagData) => TagData::fromArray($tagData), $data['tags'] ?? []);
        $this->buddies = array_map(fn ($buddyData) => BuddyData::fromArray($buddyData), $data['buddies'] ?? []);
        $this->tanks = array_map(fn ($tank) => TankData::fromArray($tank), $data['tanks'] ?? []);
    }
}
