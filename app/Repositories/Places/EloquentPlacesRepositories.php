<?php

declare(strict_types=1);

namespace App\Repositories\Places;

use App\Domain\Places\Entities\Place;
use App\Domain\Places\Repositories\PlaceRepository;
use App\Domain\Users\Repositories\CurrentUserRepository;
use App\Models\Place as PlaceModel;

final class EloquentPlacesRepositories implements PlaceRepository
{
    public function __construct(
        private CurrentUserRepository $userRepository,
    ) {
    }

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
        return PlaceModel::query()->where('country_code', $iso2)
            ->get()
            ->map(fn (PlaceModel $model) => $this->createEntityFromModel($model))
            ->toArray();
    }

    public function save(Place $place): void
    {
        if ($place->getCountryCode() !== null && $place->getName() !== null) {
            $otherPlace = $this->findByName($place->getCountryCode(), $place->getName());
            if ($otherPlace !== null) {
                $place->setId($otherPlace->getId());
                $place->setCreatedBy($otherPlace->getCreatedBy());
                return;
            }
        }

        $model = new PlaceModel();
        $model->country_code = $place->getCountryCode();
        $model->name = $place->getName();
        $model->created_by = $this->userRepository->getCurrentUser()->getId();
        $model->save();

        $place->setCreatedBy($this->userRepository->getCurrentUser()->getId());
        $place->setId($model->id);
    }

    public function findPlace(string $name, ?string $countryCode): ?Place
    {
        $queryBuilder = PlaceModel::query();
        $queryBuilder = $queryBuilder->where('name', $name);
        if ($countryCode !== null) {
            $queryBuilder = $queryBuilder->where('country_code', $countryCode);
        }
        /** @var \App\Models\Place $model */
        $model = $queryBuilder->first();

        return $model !== null ? $this->createEntityFromModel($model) : null;
    }

    private function findByName(string $countryCode, string $name): ?Place
    {
        $place = PlaceModel::query()
            ->where('country_code', $countryCode)
            ->where('name', $name)
            ->first();

        if ($place === null) {
            return null;
        }

        return $this->createEntityFromModel($place);
    }

    private function createEntityFromModel(PlaceModel $model): Place
    {
        return Place::existing(
            id: $model->id,
            name: $model->name,
            countryCode: $model->country_code,
            createdBy: $model->created_by,
        );
    }
}
