<?php

declare(strict_types=1);

namespace App\Application\Tags\Services;

use App\Application\Tags\DataTransferObjects\TagData;
use App\Domain\Tags\Entities\Tag;
use App\Domain\Tags\Repositories\TagRepository;
use App\Domain\Users\Entities\User;

final class TagProvider
{
    public function __construct(
        private TagRepository $repository
    ) {
    }

    public function make(User $user, TagData $tagData): Tag
    {
        return Tag::new($user->getId(), $tagData->getText(), $tagData->getColor());
    }

    public function findOrMake(User $user, TagData $tagData): Tag
    {
        if ($tagData->getId() === null) {
            return $this->make($user, $tagData);
        }

        return $this->repository->findById($tagData->getId());
    }
}
