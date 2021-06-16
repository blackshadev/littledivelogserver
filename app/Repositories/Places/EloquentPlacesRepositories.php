<?php

declare(strict_types=1);

namespace App\Repositories\Places;

use App\Domain\Places\Entities\Place;
use App\Domain\Places\Repositories\PlaceRepository;
use App\Models\Place as PlaceModel;

class EloquentPlacesRepositories implements PlaceRepository
{
    public function findById(int $id): Place
    {
        $place = PlaceModel::findOrFail($id);

        return $this->createEntityFromModel($place);
    }

    public function list(): array
    {
        return PlaceModel::all()
            ->map(fn (PlaceModel $model) => $this->createEntityFromModel($model))
            ->toArray();
    }

    public function forCountry(string $iso2): array
    {
        return PlaceModel::where('country_code', $iso2)
            ->map(fn (PlaceModel $model) => $this->createEntityFromModel($model))
            ->toArray();
    }

    private function createEntityFromModel(PlaceModel $model): Place
    {
        return new Place(
            id: $model->id,
            name: $model->name,
            countryCode: $model->country_code,
            createdBy: $model->created_by,
        );
    }
}
