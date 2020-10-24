<?php

declare(strict_types=1);

namespace App\Services\Repositories;

use App\DataTransferObjects\PlaceData;
use App\Error\PlaceNotFound;
use App\Models\Place;
use App\Models\User;

class PlaceRepository
{
    public function findOrCreate(PlaceData $data, User $user): Place
    {
        if ($data->getId()) {
            $place = $this->find($data->getId());

            if ($place === null) {
                throw new PlaceNotFound();
            }

            return $place;
        }

        if ($data->getCountryCode() !== null && $data->getName() !== null) {
            $place = $this->findByName($data->getCountryCode(), $data->getName());

            if ($place !== null) {
                return $place;
            }

            return $this->create($data, $user);
        }

        throw new \RuntimeException('Place data encountered without id or name');
    }

    public function find(int $id): ?Place
    {
        return Place::find($id);
    }

    public function findByName(string $countryCode, string $name): ?Place
    {
        return Place::find([
            'country_code' => $countryCode,
            'name' => $name,
        ]);
    }

    public function create(PlaceData $data, User $user): Place
    {
        $place = new Place();
        $place->fill([
            'country_code' => $data->getCountryCode(),
            'name' => $data->getName(),
        ]);
        $place->creator()->associate($user);
        $this->save($place);

        return $place;
    }

    public function save(Place $place)
    {
        $place->save();
    }
}
