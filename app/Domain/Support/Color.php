<?php

declare(strict_types=1);

namespace App\Domain\Support;

final class Color
{
    public static function randomHex(): string
    {
        return '#' . self::randomHexPart() . self::randomHexPart() . self::randomHexPart();
    }

    private static function randomHexPart(): string
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }
}
