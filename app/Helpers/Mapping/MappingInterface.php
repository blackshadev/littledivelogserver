<?php

declare(strict_types=1);


namespace App\Helpers\Mapping;

interface MappingInterface
{
    public function get($id);

    public function set($oldId, $newId);

    public function all(): \Generator;
}
