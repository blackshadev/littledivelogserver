<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\ViewModel;
use App\Domain\Places\Entities\Place;

class PlaceListViewModel extends ViewModel
{
    protected array $visible = ['place_id', 'name', 'country_code'];

    public function __construct(
        private int $id,
        private string $name,
        private string $countryCode,
    ) {
    }

    public static function fromPlace(Place $place): self
    {
        return new self(
            id: $place->getId(),
            name: $place->getName(),
            countryCode: $place->getCountryCode(),
        );
    }

    public function getPlaceId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCountryCode()
    {
        return $this->countryCode;
    }
}
