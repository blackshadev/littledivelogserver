<?php

declare(strict_types=1);

namespace App\Services\Repositories;

use App\DataTransferObjects\TagData;
use App\Error\TagNotFound;
use App\Models\Tag;
use App\Models\User;

class TagRepository
{
    public function findOrCreate(TagData $data, User $user)
    {
        if ($data->getId()) {
            $tag = $this->find($data->getId(), $user);

            if ($tag === null) {
                throw new TagNotFound();
            }

            return $tag;
        }

        if ($data->getText()) {
            $tag = $this->findByText($data->getText(), $user);

            if ($tag !== null) {
                return $tag;
            }

            return $this->create($data, $user);
        }

        throw new \RuntimeException('Tag data encountered without id or name');
    }

    public function update(Tag $tag, TagData $data)
    {
        $tag->fill([
            'text' => $data->getText(),
            'color' => $data->getColor(),
        ]);
        $this->save($tag);
    }

    public function create(TagData $tagData, User $user)
    {
        $tag = new Tag();
        $tag->fill([
            'text' => $tagData->getText(),
            'color' => $tagData->getColor(),
        ]);
        $tag->user()->associate($user);
        $this->save($tag);

        return $tag;
    }

    public function find(int $id, User $user): ?Tag
    {
        /** @var Tag|null $tag */
        return $user->tags()->find($id);
    }

    public function findByText(string $text, User $user): ?Tag
    {
        /** @var Tag|null $tag */
        return $user->tags()->where('text', $text)->first();
    }

    public function save(Tag $tag)
    {
        $tag->save();
    }
}
