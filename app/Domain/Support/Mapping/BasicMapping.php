<?php

declare(strict_types=1);

namespace App\Domain\Support\Mapping;

final class BasicMapping implements MappingInterface
{
    private $data = [];

    public function get($oldId)
    {
        return $this->data[$oldId] ?? null;
    }

    public function set($oldId, $newId): void
    {
        $this->data[$oldId] = $newId;
    }

    public function all(): \Generator
    {
        foreach ($this->data as $old => $new) {
            yield $old => $new;
        }
    }
}
