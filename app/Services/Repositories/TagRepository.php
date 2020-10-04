<?php

namespace App\Services\Repositories;

use App\DataTransferObjects\TagData;
use App\Helpers\Color;
use App\Models\Tag;
use App\Models\User;
use PhpParser\Builder;

class TagRepository
{
    public function findOrCreate(TagData $data, ?User $user = null)
    {
        /** @var Tag|Builder $scope */
        $scope = $user !== null ? $user->tags() : Tag::query();

        if ($data->getId()) {
            return $scope->findOrFail($data->getId());
        }

        if ($data->getText()) {
            return $scope->firstOrCreate([
                'text' => $data->getText(),
                'color' => $data->getColor() ?? Color::randomHex(),
                'user_id' => $user->id
            ]);
        }

        throw new \RuntimeException("Tag data encountered without id or name");
    }

    public function update(Tag $tag, TagData $data)
    {
        $tag->fill([
            'text' => $data->getText(),
            'color' => $data->getColor()
        ]);
        $tag->save();
    }
}
