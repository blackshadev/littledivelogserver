<?php

declare(strict_types=1);

namespace App\Domain\DataTransferObjects;

class EquipmentData
{
    private int $userId;

    /** @var TankData[]  */
    private array $tanks = [];

    public static function fromArray(int $userId, array $data): self
    {
        $tankData = new self();
        $tankData->userId = $userId;
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

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userid): void
    {
        $this->userId = $userid;
    }
}
