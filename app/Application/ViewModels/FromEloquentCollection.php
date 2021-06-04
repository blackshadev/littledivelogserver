<?php

declare(strict_types=1);

namespace App\Application\ViewModels;

use Illuminate\Support\Collection;

trait FromEloquentCollection
{
    public static function fromCollection(Collection $all): Collection
    {
        return $all->map(fn ($entity) => new self($entity));
    }
}
