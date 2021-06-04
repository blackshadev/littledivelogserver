<?php

declare(strict_types=1);

namespace App\Domain\Support\Mapping;

use App\Helpers\Mapping\MappingInterface;

class BasicMapping implements MappingInterface
{
    private $data = [];

    public function get($oldId)
    {
        return $this->data[$oldId] ?? null;
    }

    public function set($oldId, $newId)
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
