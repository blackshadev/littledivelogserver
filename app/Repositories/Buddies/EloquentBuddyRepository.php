<?php

declare(strict_types=1);

namespace App\Repositories\Buddies;

use App\Domain\Buddies\Entities\Buddy;
use App\Domain\Buddies\Repositories\BuddyRepository;
use App\Models\Buddy as BuddyModel;

class EloquentBuddyRepository implements BuddyRepository
{
    public function findById(int $id): Buddy
    {
        $model = BuddyModel::find($id);

        return Buddy::existing(
            id: $model->id,
            userId: $model->user_id,
            name: $model->name,
            color: $model->color,
            email: $model->email,
        );
    }

    public function save(Buddy $buddy): void
    {
        if ($buddy->isExisting()) {
            $model = BuddyModel::find($buddy->getId());
        } else {
            $model = new BuddyModel();
        }

        $model->user_id = $buddy->getUserId();
        $model->name = $buddy->getName();
        $model->color = $buddy->getColor();
        $model->email = $buddy->getEmail();

        $model->save();

        $this->setDataFromModel($buddy, $model);
    }

    private function setDataFromModel(Buddy $buddy, BuddyModel $model): void
    {
        $buddy->setId($model->id);
        $buddy->setUserId($model->user_id);
        $buddy->setName($model->name);
        $buddy->setEmail($model->email);
        $buddy->setColor($model->color);
    }
}
