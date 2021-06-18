<?php

declare(strict_types=1);

namespace App\Domain\Tags\Repositories;

use App\Domain\Tags\Entities\Tag;

interface TagRepository
{
    public function findById(int $id): Tag;

    public function save(Tag $tag): void;
}
