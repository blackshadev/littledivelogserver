<?php

declare(strict_types=1);

namespace App\Domain\Support;

class Math
{
    public static function max(...$ints)
    {
        return max(...Arrg::notNull($ints));
    }

    public static function min(...$ints)
    {
        return min(...Arrg::notNull($ints));
    }
}
