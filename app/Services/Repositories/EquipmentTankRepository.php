<?php

declare(strict_types=1);

namespace App\Services\Repositories;

use App\Domain\Equipment\DataTransferObjects\TankData;
use App\Models\EquipmentTank;

class EquipmentTankRepository
{
    public function make(TankData $tankData): EquipmentTank
    {
        $tank = new EquipmentTank();
        $this->fill($tank, $tankData);

        return $tank;
    }

    public function update(EquipmentTank  $tank, TankData $tankData)
    {
        $this->fill($tank, $tankData);
        $this->save($tank);
    }

    public function save(EquipmentTank  $tank): void
    {
        $tank->save();
    }

    public function delete(EquipmentTank  $tank): void
    {
        $tank->delete();
    }

    protected function fill(EquipmentTank $tank, TankData $tankData)
    {
        $tank->fill([
            'volume' => $tankData->getVolume(),
            'oxygen' => $tankData->getOxygen(),
            'pressure_begin' => $tankData->getPressures()->getBegin(),
            'pressure_end' => $tankData->getPressures()->getEnd(),
            'pressure_type' => $tankData->getPressures()->getType(),
        ]);
    }
}
