<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Domain\Users\Entities\User;
use App\Domain\Users\Repositories\UserRepository;
use App\Error\UserNotFound;
use App\Models\User as UserModel;

final class EloquentUserRepository implements UserRepository
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

    public function findByEmail(string $email): User
    {
        $user = UserModel::where('email', $email)->first();
        return $this->entityFromModel($user);
    }

    public function findById(int $id): User
    {
        $user = UserModel::find($id);
        return $this->entityFromModel($user);
    }

    private function entityFromModel(UserModel|null $user): User
    {
        if ($user === null) {
            throw new UserNotFound();
        }

        return new User(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            origin: $user->origin,
        );
    }
}
