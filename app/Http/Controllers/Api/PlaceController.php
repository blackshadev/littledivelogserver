<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Place;
use App\ViewModels\ApiModels\PlaceListViewModel;

class PlaceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Place::class, 'place');
    }

    public function index()
    {
        return PlaceListViewModel::fromCollection(Place::all());
    }

    public function indexForCountry(Country $country)
    {
        return PlaceListViewModel::fromCollection($country->places);
    }
}
