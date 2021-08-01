<?php

declare(strict_types=1);

namespace App\Explorer\Support;

use App\Domain\Support\Arrg;

final class Utilities
{
    public static function toArray(array $array): array
    {
        return Arrg::map($array, fn ($i) => $i->build());
    }
}
