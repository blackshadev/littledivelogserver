<?php

declare(strict_types=1);

namespace App\Domain\Places\Services;

use App\CommandObjects\FindPlaceCommand;
use App\Domain\Places\Entities\Place;

interface PlaceFinder
{
    /** @return Place[] */
    public function find(FindPlaceCommand $command): array;
}
