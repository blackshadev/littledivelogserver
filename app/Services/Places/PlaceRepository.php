<?php


namespace App\Services\Places;


use App\DataTransferObjects\PlaceData;
use App\Models\Place;

class PlaceRepository
{
    public function findOrCreate(PlaceData $data, ?User $user = null): Place
    {
        if ($data->getId()) {
            return Place::findOrFail($data->getId());
        }

        if ($data->getName() !== null) {
            return Place::firstOrCreate([
                'country_code' => $data->getCountryCode(),
                'name' => $data->getName(),
            ]);
        }

        throw new \RuntimeException("Place data encountered without id or name");
    }
}
