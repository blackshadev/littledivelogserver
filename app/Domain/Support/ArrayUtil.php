<?php

declare(strict_types=1);

namespace App\Domain\Support;

class ArrayUtil
{
    public static function transpose(array $array): array
    {
        $out = [];
        foreach ($array as $key => $subArray) {
            foreach ($subArray as $subKey => $element) {
                $out[$subKey][$key] = $element;
            }
        }
        return $out;
    }

    public static function flatten(array $array): array
    {
        return array_merge([], ...$array);
    }
}
