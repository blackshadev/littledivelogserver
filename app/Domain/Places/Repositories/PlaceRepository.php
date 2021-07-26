<?php

declare(strict_types=1);

namespace App\Domain\Places\Repositories;

use App\Domain\Places\Entities\Place;

interface PlaceRepository
{
    public function findById(int $id): Place;

    public function findPlace(string $name, string $countryCode): ?Place;

    /** @return Place[] */
    public function forCountry(string $iso2): array;

    /** @return Place[] */
    public function list(): array;

    public function save(Place $place): void;
}
