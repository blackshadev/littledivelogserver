<?php

declare(strict_types=1);

namespace App\Application\Dives\Services\Mergers;

use App\Domain\EntityWithId;

final class DiveEntityMergerImpl implements DiveEntityMerger
{
    /**
     * @param EntityWithId[] $entities
     * @return EntityWithId[]
     */
    public function unique(array $entities): array
    {
        $unique = [];

        /** @var EntityWithId $entity */
        foreach ($entities as $entity) {
            $id = $entity->getId();
            if (!isset($unique[$id])) {
                $unique[$id] = $entity;
            }
        }

        return array_values($unique);
    }
}
