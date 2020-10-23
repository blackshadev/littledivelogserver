<?php

namespace App\Helpers\Equality;

class EqualityHelpers
{
    /**
     * @param Equality[] $array
     * @param Equality $object
     */
    public static function in_array($array, Equality $object): bool
    {
        foreach ($array as $item) {
            if ($object->isEqualTo($item)) {
                return true;
            }
        }

        return false;
    }
}
