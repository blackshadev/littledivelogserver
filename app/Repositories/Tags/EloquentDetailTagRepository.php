<?php

declare(strict_types=1);

namespace App\Repositories\Tags;

use App\Domain\Tags\Entities\DetailTag;
use App\Domain\Tags\Repositories\DetailTagRepository;
use App\Models\Tag as TagModel;
use App\Models\User as UserModel;

class EloquentDetailTagRepository implements DetailTagRepository
{
    public function findById(int $id): DetailTag
    {
        $model = TagModel::find($id);

        return $this->fromModel($model);
    }

    public function listForUser(int $userId): array
    {
        $user = UserModel::find($userId);

        return $user->tags()
            ->get()
            ->map(fn (TagModel $tag) => $this->fromModel($tag))
            ->toArray();
    }

    private function fromModel(TagModel $model): DetailTag
    {
        $lastDive = $model->dives()->max('date');
        $lastDiveDate = $lastDive ? new \DateTimeImmutable($lastDive) : null;

        return new DetailTag(
            id: $model->id,
            userId: $model->user_id,
            text: $model->text,
            color: $model->color,
            diveCount: $model->dives()->count(),
            lastDive: $lastDiveDate,
        );
    }
}
