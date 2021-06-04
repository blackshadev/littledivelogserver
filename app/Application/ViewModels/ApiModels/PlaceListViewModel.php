<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\FromEloquentCollection;
use App\Application\ViewModels\ViewModel;
use App\Models\Place;

class PlaceListViewModel extends ViewModel
{
    use FromEloquentCollection;

    protected array $visible = ['place_id', 'name', 'country_code'];

    private Place $place;

    public function __construct(Place $place)
    {
        $this->place = $place;
    }

    public function getPlaceId()
    {
        return $this->place->id;
    }

    public function getName()
    {
        return $this->place->name;
    }

    public function getCountryCode()
    {
        return $this->place->country_code;
    }
}
