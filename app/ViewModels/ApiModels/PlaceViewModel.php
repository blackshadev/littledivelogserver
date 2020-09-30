<?php

namespace App\ViewModels\ApiModels;

use App\Models\Place;
use App\ViewModels\ViewModel;

class PlaceViewModel extends ViewModel
{
    protected $visible = ['country_code', 'place_id', 'name'];
    protected Place $place;

    public function __construct(Place $place)
    {
        $this->place = $place;
    }

    public function getPlaceId(): int
    {
        return $this->place->id;
    }

    public function getCountryCode(): string
    {
        return $this->place->country_code;
    }

    public function getName(): string
    {
        return $this->place->name;
    }

}
