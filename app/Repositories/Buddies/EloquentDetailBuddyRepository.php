<?php

declare(strict_types=1);

namespace App\Repositories\Buddies;

use App\Domain\Buddies\Entities\DetailBuddy;
use App\Domain\Buddies\Repositories\DetailBuddyRepository;
use App\Models\Buddy as BuddyModel;
use App\Models\User;

final class EloquentDetailBuddyRepository implements DetailBuddyRepository
{
    /** @return DetailBuddy[] */
    public function listForUser(int $userId): array
    {
        return User::findOrFail($userId)->buddies()->orderBy('id')->get()
            ->map(function (BuddyModel $model) {
                return $this->fromModel($model);
            })->toArray();
    }

    public function findById(int $id): DetailBuddy
    {
        $model = BuddyModel::findOrFail($id);
        return $this->fromModel($model);
    }

    private function fromModel(BuddyModel $model): DetailBuddy
    {
        $lastDive = $model->dives()->max('date');
        $lastDiveDate = $lastDive ? new \DateTimeImmutable($lastDive) : null;

        return new DetailBuddy(
            id: $model->id,
            userId: $model->user_id,
            name: $model->name,
            color: $model->color,
            email: $model->email,
            lastDive: $lastDiveDate,
            diveCount: $model->dives()->count(),
            updated: $model->updated_at->toDateTimeImmutable()
        );
    }
}
