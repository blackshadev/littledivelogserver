<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Domain\Users\Entities\User;
use App\Domain\Users\Repositories\UserRepository;
use App\Models\User as UserModel;

class EloquentUserRepository implements UserRepository
{
    public function save(User $user): void
    {
        if ($user->isExisting()) {
            $model = UserModel::findOrFail($user->getId());
        } else {
            throw new \LogicException('Unable to save not existing user.');
        }

        $model->name = $user->getName();

        $model->save();
    }
}
