<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Places\CommandObjects\FindPlaceCommand;
use App\Application\Places\Services\PlaceFinder;
use App\Application\Places\ViewModels\PlaceViewModel;
use App\Domain\Places\Entities\Place;
use App\Domain\Places\Repositories\PlaceRepository;
use App\Domain\Support\Arrg;
use App\Http\Controllers\Controller;
use App\Http\Requests\Places\SearchPlaceRequest;

final class PlaceController extends Controller
{
    public function __construct(
        private PlaceRepository $placeRepository,
        private PlaceFinder $placeFinder,
    ) {
    }

    public function index()
    {
        return Arrg::map(
            $this->placeRepository->list(),
            static fn (Place $place) => PlaceViewModel::fromPlace($place)
        );
    }

    public function search(SearchPlaceRequest $request)
    {
        $findCommand = FindPlaceCommand::forUser(
            $request->user()->id,
            $request->query(),
        );
        $places = $this->placeFinder->find($findCommand);

        return Arrg::map(
            $places,
            static fn (Place $place) => PlaceViewModel::fromPlace($place)
        );
    }

    public function indexForCountry(string $country)
    {
        return Arrg::map(
            $this->placeRepository->forCountry($country),
            static fn (Place $place) => PlaceViewModel::fromPlace($place)
        );
    }
}
