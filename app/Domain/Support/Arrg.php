<?php

declare(strict_types=1);

namespace App\Domain\Support;

use Illuminate\Support\Arr;

final class Arrg
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

    public static function firstNotNull(?array $a)
    {
        if ($a === null) {
            return null;
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

    public static function call(?array $a, string $method, ...$args): ?array
    {
        if ($a === null) {
            return null;
        }

        $methods = explode('.', $method);
        return Arrg::map($a, function ($item) use ($methods, $args) {
            foreach ($methods as $method) {
                $item = $item !== null ? ([$item, $method])(...$args) : null;
            }

            return $item;
        });
    }

    public static function slice(array $a, int $start, int $length)
    {
        if ($a === null) {
            return null;
        }

        return array_slice($a, $start, $length);
    }

    public static function only(?array $a, array $fields): ?array
    {
        if ($a === null) {
            return null;
        }

        return array_combine($fields, array_map(fn ($field) => $a[$field], $fields));
    }
}
