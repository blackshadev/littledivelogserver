<?php

declare(strict_types=1);

namespace App\Application\Places\Services;

use App\Application\Places\DataTransferObjects\PlaceData;
use App\Domain\Places\Entities\Place;
use App\Domain\Places\Repositories\PlaceRepository;
use App\Domain\Users\Entities\User;

final class PlaceProvider
{
    public function __construct(
        private PlaceRepository $repository,
    ) {
    }

    public function make(User $user, PlaceData $placeData): Place
    {
        return Place::new($user->getId(), $placeData->getName(), $placeData->getCountryCode());
    }

    public function findOrMake(User $user, PlaceData $placeData): Place
    {
        if ($placeData->getId() !== null) {
            return $this->repository->findById($placeData->getId());
        }

        $place = $this->repository->findPlace($placeData->getName(), $placeData->getCountryCode());

        if ($place !== null) {
            return $place;
        }

        return $this->make($user, $placeData);
    }
}
