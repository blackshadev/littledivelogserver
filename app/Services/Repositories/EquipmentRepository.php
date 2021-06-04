<?php

declare(strict_types=1);

namespace App\Services\Repositories;

use App\Domain\DataTransferObjects\EquipmentData;
use App\Domain\DataTransferObjects\TankData;
use App\Models\Equipment;
use App\Models\EquipmentTank;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EquipmentRepository
{
    private EquipmentTankRepository $tankRepository;

    public function __construct(EquipmentTankRepository $tankRepository)
    {
        $this->tankRepository = $tankRepository;
    }

    public function update(
        Equipment $equipment,
        EquipmentData $equipmentData
    ) {
        if ($equipment->user_id !== $equipmentData->getUserId()) {
            throw new \InvalidArgumentException('Unexpected userId from data');
        }

        DB::transaction(function () use ($equipment, $equipmentData) {
            $this->updateDiveTanks($equipment, $equipmentData->getTanks());
            $this->save($equipment);
        });
    }

    public function save(Equipment $equipment)
    {
        $equipment->save();
    }

    public function saveTank(EquipmentTank $tank)
    {
        $tank->save();
    }

    public function appendTank(Equipment $equipment, EquipmentTank  $tank)
    {
        $equipment->tanks()->save($tank);
    }

    public function removeTank(Equipment $equipment, EquipmentTank $tank)
    {
        $this->tankRepository->delete($tank);
    }

    public function findOrCreateForUser(User $user): Equipment
    {
        $equipment = $user->equipment;
        if ($equipment === null) {
            $equipment = new Equipment();
            $equipment->user()->associate($user);
            $this->save($equipment);
        }

        return $equipment;
    }

    /** @param TankData[] $tanks */
    protected function updateDiveTanks(Equipment $equipment, array $tanks)
    {
        $iX = 0;
        /** @var EquipmentTank $tank */
        foreach ($equipment->tanks as $tank) {
            $tankData = $tanks[$iX++] ?? null;
            if ($tankData === null) {
                $this->removeTank($equipment, $tank);
            } else {
                $this->tankRepository->update($tank, $tankData);
            }
        }

        for ($iXMax = count($tanks); $iX < $iXMax; $iX++) {
            $tankData = $tanks[$iX];
            $tank = $this->tankRepository->make($tankData);
            $this->appendTank($equipment, $tank);
        }
        $equipment->unsetRelation('tanks');
    }
}
