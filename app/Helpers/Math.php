<?php

declare(strict_types=1);


namespace App\Helpers;

class Math
{
    public static function max(...$ints)
    {
        return max(Arrg::notNull($ints));
    }

    public static function min(...$ints)
    {
        return min(Arrg::notNull($ints));
    }
}
