<?php

declare(strict_types=1);

namespace App\Domain\Support;

class ArrayUtil
{
    public static function transpose(array $array): array
    {
        return array_map(null, ...$array);
    }

    public static function flatten(array $array): array
    {
        return array_merge([], ...$array);
    }
}
