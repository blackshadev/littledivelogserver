<?php

declare(strict_types=1);

namespace App\Domain\Support\Equality;

final class EqualityHelpers
{
    /**
     * @param Equality[] $array
     * @param Equality $object
     */
    public static function inArray($array, Equality $object): bool
    {
        foreach ($array as $item) {
            if ($object->isEqualTo($item)) {
                return true;
            }
        }

        return false;
    }
}
