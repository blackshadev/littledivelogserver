<?php

declare(strict_types=1);

namespace App\Application\Tags\Services;

use App\Application\Tags\DataTransferObjects\TagData;
use App\Domain\Tags\Entities\Tag;
use App\Domain\Tags\Repositories\TagRepository;

final class TagUpdater
{
    public function __construct(
        private TagRepository $repository
    ) {
    }

    public function update(Tag $tag, TagData $tagData): void
    {
        $tag->setText($tagData->getText());
        $tag->setColor($tagData->getColor());

        $this->repository->save($tag);
    }
}
