<?php

declare(strict_types=1);

namespace App\Application\Tags\Services;

use App\Application\Tags\DataTransferObjects\TagData;
use App\Domain\Tags\Entities\Tag;
use App\Domain\Tags\Repositories\TagRepository;
use App\Domain\Users\Entities\User;

final class TagCreator
{
    public function __construct(
        private TagRepository $repository
    ) {
    }

    public function create(User $user, TagData $data): Tag
    {
        $tag = Tag::new($user->getId(), $data->getText(), $data->getColor());
        $this->repository->save($tag);

        return $tag;
    }
}
