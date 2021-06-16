<?php

declare(strict_types=1);

namespace App\Repositories\Computers;

use App\Domain\Computers\Entities\DetailComputer;
use App\Domain\Computers\Repositories\DetailComputerRepository;
use App\Models\Computer as ComputerModel;
use App\Models\User as UserModel;

class EloquentDetailComputerRepository implements DetailComputerRepository
{
    public function listForUser(int $userId): array
    {
        return UserModel::findOrFail($userId)->computers()->get()
            ->map(fn (ComputerModel $model) => $this->fromModel($model))
            ->toArray();
    }

    public function findById(int $computerId): DetailComputer
    {
        $model = ComputerModel::findOrFail($computerId);
        return $this->fromModel($model);
    }

    private function fromModel(ComputerModel $model): DetailComputer
    {
        return new DetailComputer(
            computerId: $model->id,
            name: $model->name,
            type: $model->type,
            serial: $model->serial,
            model: $model->model,
            vendor: $model->vendor,
            diveCounts: $model->dives()->count(),
            lastFingerprint: $model->last_fingerprint,
            lastRead: $model->last_read,
        );
    }
}
