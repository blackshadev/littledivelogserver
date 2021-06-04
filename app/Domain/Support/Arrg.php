<?php

declare(strict_types=1);

namespace App\Domain\Support;

use Illuminate\Support\Arr;

class Arrg
{
    public static function notNull(?array $a): ?array
    {
        return self::filter($a, fn ($item) => $item !== null);
    }

    public static function map(?array $a, callable $fn): ?array
    {
        return $a !== null ? array_map($fn, $a) : null;
    }

    public static function filter(?array $a, callable $fn): ?array
    {
        return $a !== null ? array_filter($a, $fn) : null;
    }

    public static function firstNotNull(?array $a, ?string $field = null)
    {
        if ($a === null) {
            return null;
        }
        if ($field !== null) {
            $a = Arrg::get($a, $field);
        }
        return Arr::first(Arrg::notNull($a));
    }

    public static function unique(?array $a, ?string $field = null): ?array
    {
        if ($field !== null) {
            $a = Arrg::get($a, $field);
        }

        if ($a === null) {
            return null;
        }

        return Arrg::notNull(array_unique($a));
    }

    public static function copy(?array $a): ?array
    {
        return $a !== null ? array_merge([], $a) : null;
    }

    public static function get(?array $a, string $field): ?array
    {
        if ($a === null) {
            return null;
        }

        return Arrg::map($a, fn ($item) => Arr::get($item, $field));
    }
}
