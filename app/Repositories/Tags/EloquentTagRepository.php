<?php

declare(strict_types=1);

namespace App\Repositories\Tags;

use App\Domain\Tags\DataTransferObjects\TagData;
use App\Domain\Tags\Entities\Tag;
use App\Domain\Tags\Repositories\TagRepository;
use App\Models\Tag as TagModel;

class EloquentTagRepository implements TagRepository
{
    public function findById(int $id): Tag
    {
        $model = TagModel::find($id);

        return Tag::existing(
            id: $model->id,
            userId: $model->user_id,
            text: $model->text,
            color: $model->color,
        );
    }

    public function setData(Tag $tag, TagData $tagData): void
    {
        $tag->setText($tagData->getText());
        $tag->setColor($tagData->getColor());
    }

    public function save(Tag $tag): void
    {
        if ($tag->isExisting()) {
            $model = TagModel::find($tag->getId());
        } else {
            $model = new TagModel();
        }

        $model->user_id = $tag->getUserId();
        $model->text = $tag->getText();
        $model->color = $tag->getColor();

        $model->save();

        $this->setDataFromModel($tag, $model);
    }

    public function create(int $userId, TagData $data): Tag
    {
        return Tag::new($userId, $data->getText(), $data->getColor());
    }

    private function setDataFromModel(Tag $tag, TagModel $model): void
    {
        $tag->setId($model->id);
        $tag->setUserId($model->user_id);
        $tag->setText($model->text);
        $tag->setColor($model->color);
    }
}
