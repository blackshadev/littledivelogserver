<?php

declare(strict_types=1);

namespace App\Application\Equipment\DataTransferObjects;

class EquipmentData
{
    /** @var TankData[]  */
    private array $tanks = [];

    public static function fromArray(array $data): self
    {
        $tankData = new self();
        $tankData->tanks = array_map(fn ($tank) => TankData::fromArray($tank), $data['tanks'] ?? []);

        return $tankData;
    }

    /**
     * @return TankData[]
     */
    public function getTanks(): array
    {
        return $this->tanks;
    }

    /** @param TankData[] $tanks */
    public function setTanks(array $tanks): void
    {
        $this->tanks = $tanks;
    }
}
