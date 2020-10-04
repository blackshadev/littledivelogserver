<?php

namespace App\Services\Repositories;

use App\DataTransferObjects\TankData;
use App\Models\DiveTank;

class TankRepository
{
    public function create(TankData $tankData): DiveTank
    {
        $tank = new DiveTank([
            'volume' => $tankData->getVolume(),
            'oxygen' => $tankData->getOxygen(),
            'pressure_begin' => $tankData->getPressures()->getBegin(),
            'pressure_end' => $tankData->getPressures()->getEnd(),
            'pressure_type' => $tankData->getPressures()->getType(),
        ]);

        return $tank;
    }

    public function update(DiveTank $tank, TankData $tankData)
    {
        $tank->fill([
            'volume' => $tankData->getVolume(),
            'oxygen' => $tankData->getOxygen(),
            'pressure_begin' => $tankData->getPressures()->getBegin(),
            'pressure_end' => $tankData->getPressures()->getEnd(),
            'pressure_type' => $tankData->getPressures()->getType(),
        ]);
        $tank->save();
    }

}
