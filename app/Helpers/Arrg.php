<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Arr;

class Arrg
{
    public static function notNull(array $a): array
    {
        return self::filter($a, fn ($item) => $item !== null);
    }

    public static function map(array $a, callable $fn): array
    {
        return array_map($fn, $a);
    }

    public static function filter(array $a, callable $fn): array
    {
        return array_filter($a, $fn);
    }

    public static function firstNotNull(array $a, ?string $field = null)
    {
        if ($field !== null) {
            $a = Arr::get($a, $field);
        }
        return Arr::first(Arrg::notNull($a));
    }

    public static function unique(array $a, ?string $field = null): array
    {
        if ($field !== null) {
            $a = Arr::get($a, $field);
        }
        return Arrg::notNull(array_unique($a));
    }

    public static function copy(array $a): array
    {
        return array_merge([], $a);
    }
}
