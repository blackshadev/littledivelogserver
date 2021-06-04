<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\ViewModels\ApiModels\PlaceListViewModel;
use App\CommandObjects\FindPlaceCommand;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Place;
use App\Services\Repositories\PlaceRepository;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    private PlaceRepository $placeRepository;

    public function __construct(PlaceRepository $placeRepository)
    {
        $this->placeRepository = $placeRepository;
        $this->authorizeResource(Place::class, 'place');
    }

    public function index()
    {
        return PlaceListViewModel::fromCollection(Place::all());
    }

    public function search(Request $request)
    {
        $findCommand = FindPlaceCommand::fromArray($request->query());
        $places = $this->placeRepository->find($findCommand);
        return PlaceListViewModel::fromCollection($places);
    }

    public function indexForCountry(Country $country)
    {
        return PlaceListViewModel::fromCollection($country->places);
    }
}
