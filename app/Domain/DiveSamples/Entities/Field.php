<?php

declare(strict_types=1);

namespace App\Domain\DiveSamples\Entities;

enum Field: string
{
    case Time = 'Time';
    case Depth = 'Depth';
    case Pressure = 'Pressure';
}
