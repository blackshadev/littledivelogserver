<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Domain\Users\Entities\DetailUser;
use App\Domain\Users\Repositories\DetailUserRepository;
use App\Models\User as UserModel;

final class EloquentDetailUserRepository implements DetailUserRepository
{
    public function findById(int $id): DetailUser
    {
        $model = UserModel::findOrFail($id);

        return new DetailUser(
            userId: $model->id,
            name: $model->name,
            email: $model->email,
            inserted: $model->created_at,
            tagCount: $model->tags()->count(),
            computerCount: $model->computers()->count(),
            buddyCount: $model->buddies()->count(),
            diveCount: $model->dives()->count(),
        );
    }
}
