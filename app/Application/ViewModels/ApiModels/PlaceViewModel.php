<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\ViewModel;
use App\Models\Place;

class PlaceViewModel extends ViewModel
{
    protected array $visible = ['country_code', 'place_id', 'name'];

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
