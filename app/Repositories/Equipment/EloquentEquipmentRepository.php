<?php

declare(strict_types=1);

namespace App\Repositories\Equipment;

use App\Application\Equipment\DataTransferObjects\EquipmentData;
use App\Application\Equipment\DataTransferObjects\TankData;
use App\Domain\Equipment\Entities\Equipment;
use App\Domain\Equipment\Entities\Tank;
use App\Domain\Equipment\Repositories\EquipmentRepository;
use App\Domain\Support\Arrg;
use App\Models\Equipment as EquipmentModel;
use App\Models\EquipmentTank as EquipmentTankModel;
use Illuminate\Support\Facades\DB;

final class EloquentEquipmentRepository implements EquipmentRepository
{
    public function save(Equipment $equipment): void
    {
        DB::transaction(function () use ($equipment): void {
            /** @var EquipmentModel $model */
            if ($equipment->isExisting()) {
                $model = EquipmentModel::findOrFail($equipment->getId());
            } else {
                $model = EquipmentModel::create();
            }

            $iX = 0;
            /** @var EquipmentTankModel $tankModel */
            foreach ($model->tanks()->get() as $tankModel) {
                $tank = $equipment->getTank($iX++);
                if ($tank !== null) {
                    $this->updateTank($tankModel, $tank);
                } else {
                    $tankModel->delete();
                }
            }

            $tankCount = count($equipment->getTanks());
            for (; $iX < $tankCount; $iX++) {
                $tank = $equipment->getTank($iX);
                $tankModel = $this->makeTank($equipment);
                $this->updateTank($tankModel, $tank);
            }
        });
    }

    public function setData(Equipment $equipment, EquipmentData $data): void
    {
        $this->updateDiveTanks($equipment, $data->getTanks());
    }

    public function forUser(int $userId): Equipment
    {
        /** @var EquipmentModel $equipment */
        $equipment = EquipmentModel::where('user_id', $userId)->first();

        if ($equipment === null) {
            return Equipment::new($userId, []);
        }

        $tanks = $equipment->tanks()->get()
            ->map(fn (EquipmentTankModel $tank) => Tank::existing(
                id: $tank->id,
                pressureType: $tank->pressure_type,
                beginPressure: $tank->pressure_begin,
                endPressure: $tank->pressure_end,
                oxygen: $tank->oxygen,
                volume: $tank->volume,
            ))
            ->toArray();

        return Equipment::existing(
            id: $equipment->id,
            userId: $equipment->user_id,
            tanks: $tanks
        );
    }

    private function makeTank(Equipment $equipment): EquipmentTankModel
    {
        $tankModel = new EquipmentTankModel();
        $tankModel->setRawAttributes(['equipment_id' => $equipment->getId()]);
        return $tankModel;
    }

    private function updateTank(EquipmentTankModel $tankModel, Tank $tank): void
    {
        $tankModel->fill([
            'volume' => $tank->getVolume(),
            'oxygen' => $tank->getOxygen(),
            'pressure_begin' => $tank->getBeginPressure(),
            'pressure_end' => $tank->getEndPressure(),
            'pressure_type' => $tank->getPressureType(),
        ]);
        $tankModel->save();
        $tank->setId($tankModel->id);
    }

    /** @param TankData[] $tanksData */
    private function updateDiveTanks(Equipment $equipment, array $tanksData): void
    {
        $newTanks = Arrg::slice($equipment->getTanks(), 0, count($tanksData));

        /** @var Tank $tank */
        for ($iX = 0, $iXMax = count($newTanks); $iX < $iXMax; $iX++) {
            $this->setTankData($newTanks[$iX], $tanksData[$iX]);
        }

        for ($iXMax = count($tanksData); $iX < $iXMax; $iX++) {
            $tankData = $tanksData[$iX];
            $newTanks[] = Tank::new(
                volume: $tankData->getVolume(),
                oxygen: $tankData->getOxygen(),
                beginPressure: $tankData->getPressures()->getBegin(),
                endPressure: $tankData->getPressures()->getEnd(),
                pressureType: $tankData->getPressures()->getType(),
            );
        }

        $equipment->setTanks($newTanks);
    }

    private function setTankData(Tank $tank, TankData $tankData): void
    {
        $tank->setBeginPressure($tankData->getPressures()->getBegin());
        $tank->setEndPressure($tankData->getPressures()->getEnd());
        $tank->setPressureType($tankData->getPressures()->getType());
        $tank->setOxygen($tankData->getOxygen());
        $tank->setVolume($tankData->getVolume());
    }
}
