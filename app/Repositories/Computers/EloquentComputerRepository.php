<?php

declare(strict_types=1);

namespace App\Repositories\Computers;

use App\Domain\Computers\Entities\Computer;
use App\Domain\Computers\Repositories\ComputerRepository;
use App\Domain\Users\Entities\User;
use App\Models\Computer as ComputerModel;

class EloquentComputerRepository implements ComputerRepository
{
    public function findById(int $id): Computer
    {
        $model = ComputerModel::findOrFail($id);

        return $this->fromModel($model);
    }

    public function findBySerial(User $user, int $serial): ?Computer
    {
        $model = ComputerModel::query()
            ->where('user_id', $user->getId())
            ->where('serial', $serial)
            ->first();

        return !is_null($model) ? $this->fromModel($model) : null;
    }

    public function save(Computer $computer): void
    {
        if ($computer->isExisting()) {
            $model = ComputerModel::find($computer->getId());
        } else {
            $model = new ComputerModel();
        }

        $model->user_id = $computer->getUserId();
        $model->name = $computer->getName();
        $model->serial = $computer->getSerial();
        $model->type = $computer->getType();
        $model->vendor = $computer->getVendor();
        $model->last_read = $computer->getLastRead();
        $model->last_fingerprint = $computer->getFingerprint();
        $model->setAttribute('model', $computer->getModel());

        $model->save();

        $this->setDataFromModel($computer, $model);
    }

    private function fromModel(ComputerModel $model): Computer
    {
        return new Computer(
            userId: $model->user_id,
            computerId: $model->id,
            name: $model->name,
            vendor: $model->vendor,
            model: $model->getAttribute('model'),
            type: $model->type,
            serial: $model->serial,
            lastRead: $model->last_read,
            fingerprint: $model->last_fingerprint,
        );
    }

    private function setDataFromModel(Computer $computer, ComputerModel $model): void
    {
        $computer->setId($model->id);
        $computer->setUserId($model->user_id);
        $computer->setName($model->name);
        $computer->setType($model->type);
        $computer->setModel($model->getAttribute('model'));
        $computer->setSerial($model->serial);
        $computer->setVendor($model->vendor);
    }
}
