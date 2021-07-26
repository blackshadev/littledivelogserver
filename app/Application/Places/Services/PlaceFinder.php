<?php

declare(strict_types=1);

namespace App\Application\Places\Services;

use App\Application\Places\CommandObjects\FindPlaceCommand;
use App\Domain\Places\Entities\Place;

interface PlaceFinder
{
    /** @return Place[] */
    public function find(FindPlaceCommand $command): array;
}
