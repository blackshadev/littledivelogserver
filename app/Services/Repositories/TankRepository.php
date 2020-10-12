<?php

namespace App\Services\Repositories;

use App\DataTransferObjects\TankData;
use App\Models\DiveTank;

class TankRepository
{
    public function make(TankData $tankData): DiveTank
    {
        $tank = new DiveTank();
        $this->fill($tank, $tankData);

        return $tank;
    }

    public function update(DiveTank $tank, TankData $tankData)
    {
        $this->fill($tank, $tankData);
        $this->save($tank);
    }

    public function save(DiveTank $tank): void
    {
        $tank->save();
    }

    public function delete(DiveTank $tank): void
    {
        $tank->delete();
    }

    protected function fill(DiveTank $tank, TankData $tankData)
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
