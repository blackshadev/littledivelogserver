<?php

declare(strict_types=1);

namespace App\Repositories\Dives;

use App\Domain\Dives\Entities\DiveTank;
use App\Domain\Dives\Repositories\DiveTankRepository;
use App\Domain\Dives\ValueObjects\GasMixture;
use App\Domain\Dives\ValueObjects\TankPressures;
use App\Models\DiveTank as DiveTankModel;

final class EloquentDiveTankRepository implements DiveTankRepository
{
    public function findById(int $id): DiveTank
    {
        $model = DiveTankModel::findOrFail($id);
        return $this->createFromModel($model);
    }

    public function remove(DiveTank $diveTank): void
    {
        if (!$diveTank->isExisting()) {
            return;
        }

        $model = DiveTankModel::findOrFail($diveTank->getId());
        $model->delete();
    }

    public function save(DiveTank $diveTank): void
    {
        if (!$diveTank->isExisting()) {
            $model = new DiveTankModel();
        } else {
            $model = DiveTankModel::findOrFail($diveTank->getId());
        }

        $model->volume = $diveTank->getVolume();
        $model->oxygen = $diveTank->getGasMixture()->getOxygen();
        $model->pressure_begin = $diveTank->getPressures()->getBegin();
        $model->pressure_end = $diveTank->getPressures()->getEnd();
        $model->pressure_type = $diveTank->getPressures()->getType();
        $model->dive_id = $diveTank->getDiveId();
        $model->save();

        $diveTank->setId($model->id);
    }

    private function createFromModel(DiveTankModel $model): DiveTank
    {
        return DiveTank::existing(
            id: $model->id,
            diveId: $model->dive_id,
            volume: $model->volume,
            gasMixture: new GasMixture(
                oxygen: $model->oxygen
            ),
            pressures: new TankPressures(
                begin: $model->pressure_begin,
                end: $model->pressure_end,
                type: $model->pressure_type
            ),
        );
    }
}
