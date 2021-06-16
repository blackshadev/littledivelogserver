<?php

declare(strict_types=1);

namespace App\Services\Repositories;

use App\CommandObjects\FindPlaceCommand;
use App\Domain\Places\DataTransferObjects\PlaceData;
use App\Error\PlaceNotFound;
use App\Models\Place;
use App\Models\User;
use Illuminate\Support\Collection;
use JeroenG\Explorer\Domain\Syntax\Compound\BoolQuery;
use JeroenG\Explorer\Domain\Syntax\Matching;
use JeroenG\Explorer\Domain\Syntax\Term;
use Laravel\Scout\Builder;

class PlaceRepository
{
    public function findOrCreate(PlaceData $data, User $user): Place
    {
        if ($data->getId()) {
            $place = $this->findById($data->getId());

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

    public function search(Builder $search): Collection
    {
        if (!$search->model instanceof Place) {
            throw new \RuntimeException('Invalid search builder, expected search for model Place. Got ' . get_class($search->model));
        }

        return $search->get();
    }

    public function find(FindPlaceCommand $command): Collection
    {
        $search = Place::search();
        if ($command->getCountry()) {
            $search->filter(new Term('country_code', $command->getCountry(), null));
        }
        if ($command->getKeywords()) {
            $query = new BoolQuery();
            $query->should(new Matching('name', $command->getKeywords()));
            $query->should(new Matching('country', $command->getKeywords()));
            $search->must($query);
        }

        return $this->search($search);
    }

    public function findById(int $id): ?Place
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
