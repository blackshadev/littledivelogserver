<?php

namespace App\DataTransferObjects;

class TankData
{
    private ?int $volume;
    private ?int $oxygen;
    private TankPressureData $pressures;

    public static function fromArray(array $data): self
    {
        $tankData = new TankData();
        $tankData->volume = $data['volume'] ?? null;
        $tankData->oxygen = $data['oxygen'] ?? null;
        $tankData->pressures = TankPressureData::fromArray($data['pressure']);
        return $tankData;
    }

    public function __construct()
    {
        $this->pressures = new TankPressureData();
    }

    public function setVolume(?int $volume): void
    {
        $this->volume = $volume;
    }

    public function setOxygen(?int $oxygen): void
    {
        $this->oxygen = $oxygen;
    }

    public function getPressures(): TankPressureData
    {
        return $this->pressures;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function getOxygen(): ?int
    {
        return $this->oxygen;
    }
}
